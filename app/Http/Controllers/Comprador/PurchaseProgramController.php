<?php

namespace App\Http\Controllers\Comprador;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use Illuminate\Http\Request;

use App\Models\PurchaseProgram;
use App\Models\PurchaseProgramItem;
use App\Models\PurchaseProgramAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseProgramExport;

class PurchaseProgramController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $statusFilter = $request->get('status');
        $search = $request->get('search');

        $query = PurchaseProgram::with(['requisition.client', 'requisition.user'])
            ->where('company_id', $company->id)
            ->orderByDesc('created_at');

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('requisition', function ($sub) use ($search) {
                        $sub->where('code', 'like', "%{$search}%")
                            ->orWhere('requester_name', 'like', "%{$search}%");
                    });
            });
        }

        $programs = $query->paginate(15);

        // lista de status possíveis para filtros
        $availableStatuses = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'concluido' => 'Concluído',
        ];

        return view('comprador.programs.index', compact(
            'programs',
            'company',
            'statusFilter',
            'availableStatuses',
            'search'
        ));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisitionId = $request->get('requisition_id');

        $requisition = Requisition::with(['items.product.unit', 'client', 'user'])
            ->where('company_id', $company->id)
            ->findOrFail($requisitionId);

        // só permitir requisições aprovadas ou parciais
        if (!in_array($requisition->status, ['aprovado', 'parcial'])) {
            return redirect()
                ->route('comprador.requisitions.index')
                ->with('warning', 'Só é possível criar programação de compra para requisições aprovadas ou parciais.');
        }

        return view('comprador.programs.create', compact('requisition', 'company', 'user'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $validated = $request->validate([
            'requisition_id' => ['required', 'integer', Rule::exists('requisitions', 'id')],
            'buyer_notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.requisition_item_id' => ['required', 'integer', Rule::exists('requisition_items', 'id')],
            'items.*.payment_method' => ['nullable', 'string', 'max:100'],
            'items.*.supplier_name' => ['nullable', 'string', 'max:255'],
            'items.*.budget_unit_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ], [
            'items.required' => 'Adicione pelo menos um produto na programação.',
            'items.min' => 'Adicione pelo menos um produto na programação.',
        ]);

        $requisition = Requisition::with(['items.product.unit', 'client', 'user'])
            ->where('company_id', $company->id)
            ->findOrFail($validated['requisition_id']);

        if (!in_array($requisition->status, ['aprovado', 'parcial'])) {
            return redirect()
                ->route('comprador.requisitions.index')
                ->with('warning', 'Só é possível criar programação de compra para requisições aprovadas ou parciais.');
        }

        DB::transaction(function () use ($validated, $requisition, $user, $company) {

            $nextId = (PurchaseProgram::max('id') ?? 0) + 1;
            $code = 'PPG-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $program = PurchaseProgram::create([
                'company_id' => $company->id,
                'requisition_id' => $requisition->id,
                'buyer_id' => $user->id,
                'code' => $code,
                'status' => 'pendente',
                'buyer_name' => $user->name,
                'buyer_phone' => $user->phone ?? null,
                'buyer_email' => $user->email,
                'buyer_company_id' => $company->id,
                'notes' => $validated['buyer_notes'] ?? null,
                'total_budget_value' => 0,
            ]);

            $totalGeral = 0;
            $methodTotals = []; // ['TPA' => 20000, 'TRANSFERENCIA' => 10000, ...]

            foreach ($validated['items'] as $itemData) {
                $reqItem = $requisition->items->firstWhere('id', (int) $itemData['requisition_item_id']);

                if (!$reqItem) {
                    continue;
                }

                $unitValue = isset($itemData['budget_unit_value'])
                    ? (float) $itemData['budget_unit_value']
                    : 0;

                $qty = (float) $reqItem->requested_quantity;
                $totalValue = $unitValue * $qty;
                $totalGeral += $totalValue;

                $method = trim($itemData['payment_method'] ?? '');
                if ($method === '') {
                    $method = 'SEM DEFINIÇÃO';
                }
                $methodKey = mb_strtoupper($method, 'UTF-8');

                if (!isset($methodTotals[$methodKey])) {
                    $methodTotals[$methodKey] = 0;
                }
                $methodTotals[$methodKey] += $totalValue;

                PurchaseProgramItem::create([
                    'purchase_program_id' => $program->id,
                    'requisition_item_id' => $reqItem->id,
                    'product_id' => $reqItem->product_id,
                    'payment_method' => $itemData['payment_method'] ?? null,
                    'supplier_name' => $itemData['supplier_name'] ?? null,
                    'budget_unit_value' => $unitValue,
                    'budget_total_value' => $totalValue,
                    'status' => 'pendente',
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $program->update([
                'total_budget_value' => $totalGeral,
            ]);

            // Se um dia quiser salvar os $methodTotals em outra tabela/JSON, já está pronto
        });

        return redirect()
            ->route('comprador.dashboard')
            ->with('success', 'Programação de compra criada com sucesso e enviada para o Diretor (status pendente).');
    }

    public function show(PurchaseProgram $program)
    {
        $user = auth()->user();
        $company = $user->company;

        // garantir que é da mesma empresa
        if ($program->company_id !== $company->id) {
            abort(403, 'Não autorizado.');
        }

        // carregar relações necessárias
        $program->load([
            'requisition.client',
            'requisition.user',
            'requisition.items.product.unit',
            'items.product.unit',
            'attachments',
        ]);

        return view('comprador.programs.show', compact('program', 'company', 'user'));
    }

    public function edit(PurchaseProgram $program)
    {
        $user = auth()->user();
        $company = $user->company;

        if ($program->company_id !== $company->id) {
            abort(403, 'Não autorizado.');
        }

        // regra: comprador não pode editar se status for aprovado ou concluido
        if (in_array($program->status, ['aprovado', 'concluido'])) {
            return redirect()
                ->route('comprador.programs.show', $program->id)
                ->with('warning', 'Não é possível editar uma programação aprovada ou concluída.');
        }

        $program->load([
            'requisition.client',
            'requisition.user',
            'requisition.items.product.unit',
            'items.product.unit',
        ]);

        return view('comprador.programs.edit', compact('program', 'company', 'user'));
    }

    public function update(Request $request, PurchaseProgram $program)
    {
        $user = auth()->user();
        $company = $user->company;

        if ($program->company_id !== $company->id) {
            abort(403, 'Não autorizado.');
        }

        if (in_array($program->status, ['aprovado', 'concluido'])) {
            return redirect()
                ->route('comprador.programs.show', $program->id)
                ->with('warning', 'Não é possível editar uma programação aprovada ou concluída.');
        }

        $validated = $request->validate([
            'buyer_phone' => ['nullable', 'string', 'max:100'],
            'buyer_email' => ['nullable', 'email', 'max:255'],
            'buyer_notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:purchase_program_items,id'],
            'items.*.payment_method' => ['nullable', 'string', 'max:100'],
            'items.*.supplier_name' => ['nullable', 'string', 'max:255'],
            'items.*.budget_unit_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $program, $request) {

            // Atualiza cabeçalho
            $program->update([
                'buyer_phone' => $validated['buyer_phone'] ?? null,
                'buyer_email' => $validated['buyer_email'] ?? $program->buyer_email,
                'notes' => $validated['buyer_notes'] ?? null,
            ]);

            $totalGeral = 0;

            // Atualiza itens
            foreach ($validated['items'] as $itemData) {
                $item = $program->items()->where('id', $itemData['id'])->first();

                if (!$item) {
                    continue;
                }

                $unitValue = isset($itemData['budget_unit_value']) ? (float) $itemData['budget_unit_value'] : 0;
                $qty = $item->requisitionItem?->requested_quantity ?? 0;
                $totalValue = $unitValue * $qty;
                $totalGeral += $totalValue;

                $item->update([
                    'payment_method' => $itemData['payment_method'] ?? null,
                    'supplier_name' => $itemData['supplier_name'] ?? null,
                    'budget_unit_value' => $unitValue,
                    'budget_total_value' => $totalValue,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('comprador.programs.show', $program->id)
            ->with('success', 'Programação de compra atualizada com sucesso.');
    }

    public function exportPdf(PurchaseProgram $program)
    {
        $user = auth()->user();
        $company = $user->company;

        if ($program->company_id !== $company->id)
            abort(403);

        $program->load(['requisition.client', 'requisition.user', 'items.product.unit', 'items.requisitionItem', 'attachments']);

        // calcula totais por método (opcional passar à view)
        $methodTotals = [];
        $grandTotal = 0;
        foreach ($program->items as $item) {
            $m = trim(strtoupper($item->payment_method ?? 'SEM DEFINIÇÃO'));
            $v = $item->budget_total_value ?? 0;
            $methodTotals[$m] = ($methodTotals[$m] ?? 0) + $v;
            $grandTotal += $v;
        }

        $pdf = Pdf::loadView('comprador.programs.pdf', [
            'program' => $program,
            'company' => $company,
            'user' => $user,
            'methodTotals' => $methodTotals,
            'grandTotal' => $grandTotal,
        ])->setPaper('a4', 'landscape');

        // habilitar opções úteis (HTML5 parser e remote assets)
        Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download("programacao-{$program->code}.pdf");
    }

    public function exportExcel(PurchaseProgram $program)
    {
        $user = auth()->user();
        $company = $user->company;

        if ($program->company_id !== $company->id) {
            abort(403, 'Não autorizado.');
        }

        $fileName = 'programacao-' . $program->code . '.xlsx';

        return Excel::download(new PurchaseProgramExport($program), $fileName);
    }

}
