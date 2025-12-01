<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\PurchaseProgram;
use App\Models\PurchaseProgramAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseProgramExport;

class MotoristaProgramController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'aprovado');
        $search = $request->get('search');

        $query = PurchaseProgram::with(['requisition', 'company'])
            ->whereIn('status', ['aprovado', 'parcial', 'concluido']);

        if ($status && $status !== 'todos') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('requisition', function ($sq) use ($search) {
                        $sq->where('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('company', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $programs = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('motorista.motoristaPrograms.index', compact(
            'programs',
            'status',
            'search'
        ));
    }

    public function show(PurchaseProgram $program)
    {
        $program->load([
            'company',
            'requisition.client',
            'requisition.user',
            'items.product.unit',
            'items.requisitionItem', // <- ADD ISTO
            'attachments',
        ]);

        return view('motorista.motoristaPrograms.show', compact('program'));
    }

    public function conclude(Request $request, PurchaseProgram $program)
    {
        if (!in_array($program->status, ['aprovado', 'parcial'])) {
            return redirect()
                ->route('motorista.motoristaPrograms.show', $program->id)
                ->with('info', 'Esta programação não pode ser alterada, pois já está concluída.');
        }

        $markedItems = $request->input('items', []);

        DB::transaction(function () use ($program, $markedItems) {
            $items = $program->items()->get();
            $treatAsAllMarked = empty($markedItems);

            foreach ($items as $item) {
                if ($treatAsAllMarked) {
                    $item->status = 'concluido';
                } else {
                    if (in_array($item->id, $markedItems)) {
                        $item->status = 'concluido';
                    } else {
                        $item->status = 'faltando';
                    }
                }
                $item->save();
            }

            $totalItems = $items->count();
            $completedItems = $items->where('status', 'concluido')->count();

            $program->status = $completedItems === $totalItems ? 'concluido' : 'parcial';
            $program->save();
        });

        return redirect()
            ->route('motorista.motoristaPrograms.show', $program->id)
            ->with('success', 'Programação atualizada com sucesso conforme a conclusão dos itens.');
    }

    /**
     * Upload de anexo (motorista).
     */
    public function uploadAttachment(Request $request, PurchaseProgram $program)
    {
        if ($program->status === 'concluido') {
            return back()->with('error', 'Não é permitido enviar anexos para uma programação concluída.');
        }

        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['file', 'max:10240'], // 10MB por arquivo
        ]);

        if (!$request->hasFile('files')) {
            return back()->with('error', 'Nenhum arquivo foi enviado.');
        }

        foreach ($request->file('files') as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $path = $file->store('purchase_program_attachments', 'public');

            PurchaseProgramAttachment::create([
                'purchase_program_id' => $program->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
            ]);
        }

        return back()->with('success', 'Anexos enviados com sucesso.');
    }

    public function downloadAttachment(PurchaseProgramAttachment $attachment)
    {
        $path = storage_path('app/public/' . $attachment->path);

        if (!file_exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->download($path, $attachment->original_name);
    }

    public function deleteAttachment(PurchaseProgramAttachment $attachment)
    {
        $program = $attachment->program; // relação belongsTo

        if ($program && $program->status === 'concluido') {
            return back()->with('error', 'Não é permitido remover anexos de uma programação concluída.');
        }

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Anexo removido com sucesso.');
    }

    public function exportPdf(PurchaseProgram $program)
    {
        $user = auth()->user();

        $program->load([
            'company',
            'requisition.client',
            'requisition.user',
            'items.product.unit',
            'items.requisitionItem',
            'attachments',
        ]);

        // totais por método
        $methodTotals = [];
        $grandTotal = 0;

        foreach ($program->items as $item) {
            $method = trim(strtoupper($item->payment_method ?? 'SEM DEFINIÇÃO'));
            $value = $item->budget_total_value ?? 0;
            $methodTotals[$method] = ($methodTotals[$method] ?? 0) + $value;
            $grandTotal += $value;
        }

        $pdf = Pdf::loadView('motorista.motoristaPrograms.pdf', [
            'program' => $program,
            'company' => $program->company,
            'user' => $user,
            'methodTotals' => $methodTotals,
            'grandTotal' => $grandTotal,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("programacao-motorista-{$program->code}.pdf");
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
