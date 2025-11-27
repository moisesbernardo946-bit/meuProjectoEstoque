<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\EntityProduct;
use App\Models\EntryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RequisitionExport;

class RequisitionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $search = $request->input('search');
        $status = $request->input('status');
        $priority = $request->input('priority');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Requisition::with(['client', 'items'])
            ->where('company_id', $company->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('requester_name', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $requisitions = $query->orderBy('id', 'desc')->paginate(15);

        return view('almoxarife.requisitions.index', compact(
            'requisitions',
            'search',
            'status',
            'priority',
            'dateFrom',
            'dateTo'
        ));
    }

    public function create()
    {
        $user = auth()->user();
        $company = $user->company;

        $clients = Client::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')->get();

        return view('almoxarife.requisitions.create', compact(
            'company',
            'clients',
            'products'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $validated = $request->validate([
            'client_selector' => ['required', 'string'],
            'requester_name' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(['baixa', 'media', 'alta', 'urgente'])],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'items.*.requested_quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Adicione pelo menos um item na requisição.',
            'items.min' => 'Adicione pelo menos um item na requisição.',
        ]);

        // Decodifica o cliente/destinatário
        [$entityType, $entityId] = explode(':', $validated['client_selector']);

        if ($entityType === 'company') {
            // Cliente é a própria empresa (requisição interna)
            $clientId = null;
            $entityTypeFinal = 'company';
            $entityIdFinal = $company->id;
        } else {
            // Cliente é um cliente da empresa
            $clientId = (int) $entityId;
            $entityTypeFinal = 'client';
            $entityIdFinal = $clientId;
        }

        // ===================== REGRA NOVA AQUI =====================
        // Não permitir requisição para produtos que NÃO existam em entity_products
        $productIds = collect($validated['items'])
            ->pluck('product_id')
            ->unique()
            ->values()
            ->all();

        // Busca todos os entity_products existentes para essa entidade + lista de produtos
        $existingEntityProducts = EntityProduct::where('company_id', $company->id)
            ->where('entity_type', $entityTypeFinal)
            ->where('entity_id', $entityIdFinal)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        // Verifica quais produtos estão faltando
        $missingProductIds = array_diff($productIds, $existingEntityProducts);

        if (!empty($missingProductIds)) {
            // Opcional: buscar os nomes dos produtos faltantes para a mensagem de erro
            $missingProducts = Product::whereIn('id', $missingProductIds)->get();

            $messages = $missingProducts->map(function ($prod) {
                return "O produto {$prod->code} - {$prod->name} não está associado a este cliente/empresa na tabela de estoque (entity_products).";
            })->toArray();

            // Lança ValidationException com mensagem geral + detalhes
            throw \Illuminate\Validation\ValidationException::withMessages([
                'items' => "Não é possível criar a requisição. Existem produtos não associados a este cliente/empresa na tabela de estoque.",
                'items_missing' => implode(" ", $messages),
            ]);
        }
        // =================== FIM DA REGRA NOVA ====================

        DB::transaction(function () use ($validated, $user, $company, $clientId) {
            $nextId = (Requisition::max('id') ?? 0) + 1;
            $code = 'REQ-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $requisition = Requisition::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'client_id' => $clientId, // pode ser null se for a própria empresa
                'requester_name' => $validated['requester_name'],
                'code' => $code,
                'priority' => $validated['priority'],
                'status' => 'pendente',
                'purpose' => $validated['purpose'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'product_id' => $itemData['product_id'],
                    'requested_quantity' => $itemData['requested_quantity'],
                    'delivered_quantity' => 0,
                    'notes' => $itemData['notes'] ?? null,
                    'item_status' => 'pendente',
                    'rejection_reason' => null,
                ]);
            }
        });

        return redirect()
            ->route('almoxarife.requisitions.index')
            ->with('success', 'Requisição criada com sucesso.');
    }

    public function getClientProducts(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $request->validate([
            'client_selector' => ['required', 'string'],
        ]);

        // Decodificar client_selector: "company:ID" ou "client:ID"
        [$entityType, $entityId] = explode(':', $request->client_selector);

        if ($entityType === 'company') {
            // Requisição interna para a própria empresa
            $entityTypeDb = 'company';
            $entityIdDb = (int) $entityId;
        } else {
            // Cliente da empresa
            $entityTypeDb = 'client';
            $entityIdDb = (int) $entityId;
        }

        $entityProducts = EntityProduct::with('product.unit')
            ->where('company_id', $company->id)
            ->where('entity_type', $entityTypeDb)
            ->where('entity_id', $entityIdDb)
            ->orderBy('id')
            ->get();

        // Montar payload simples pro front
        $items = $entityProducts->map(function ($ep) {
            return [
                'entity_product_id' => $ep->id,
                'product_id' => $ep->product_id,
                'product_code' => $ep->product->code ?? '',
                'product_name' => $ep->product->name ?? '',
                'unit_name' => optional($ep->product->unit)->symbol ?? '',
                'min_stock' => $ep->min_stock,
                'max_stock' => $ep->max_stock,
                'current_quantity' => $ep->quantity,
            ];
        });

        return response()->json([
            'items' => $items,
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with(['client', 'items.product', 'user'])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        return view('almoxarife.requisitions.show', compact('requisition'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with(['items.product', 'client'])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        if ($requisition->status !== 'pendente') {
            return redirect()
                ->route('almoxarife.requisitions.show', $requisition->id)
                ->with('error', 'Somente requisições pendentes podem ser editadas.');
        }

        $clients = Client::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')->get();

        return view('almoxarife.requisitions.edit', compact(
            'requisition',
            'clients',
            'products'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with('items')
            ->where('company_id', $company->id)
            ->findOrFail($id);

        if ($requisition->status !== 'pendente') {
            return redirect()
                ->route('almoxarife.requisitions.show', $requisition->id)
                ->with('error', 'Somente requisições pendentes podem ser editadas.');
        }

        // 1) Sanitizar itens: remover linhas vazias e forçar cast de quantidade
        $rawItems = $request->input('items', []);
        $itemsClean = [];

        foreach ($rawItems as $row) {
            // se não existir product_id ou estiver vazio, ignoramos a linha
            if (empty($row['product_id'])) {
                continue;
            }

            // normaliza requested_quantity: se vazio ou não-numeric -> null (vai falhar na validação)
            $rq = $row['requested_quantity'] ?? null;
            if ($rq === '' || $rq === null) {
                $rqClean = null;
            } else {
                // remove vírgulas, espaços e força inteiro
                $rqClean = is_numeric($rq) ? (int) $rq : null;
            }

            $itemsClean[] = [
                'id' => $row['id'] ?? null,
                'product_id' => (int) $row['product_id'],
                'requested_quantity' => $rqClean,
                'notes' => $row['notes'] ?? null,
            ];
        }

        // Re-inserir os itens saneados no request para validação
        $request->merge(['items' => $itemsClean]);

        // 2) Validar (agora com dados limpos)
        $validated = $request->validate([
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')],
            'requester_name' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(['baixa', 'media', 'alta', 'urgente'])],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:requisition_items,id'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'items.*.requested_quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Adicione pelo menos um item na requisição.',
            'items.min' => 'Adicione pelo menos um item na requisição.',
        ]);

        // 3) Persistir em transação
        DB::transaction(function () use ($validated, $requisition) {
            // Atualiza cabeçalho
            $requisition->update([
                'client_id' => $validated['client_id'],
                'requester_name' => $validated['requester_name'],
                'priority' => $validated['priority'],
                'purpose' => $validated['purpose'] ?? null,
                'notes' => $validated['notes'] ?? null,
                // status continua o mesmo (pendente)
            ]);

            // tratar itens: atualizar, criar, remover
            $existingIds = $requisition->items->pluck('id')->toArray();
            $sentIds = [];

            foreach ($validated['items'] as $itemData) {
                if (!empty($itemData['id'])) {
                    // Atualiza item existente
                    $item = RequisitionItem::where('requisition_id', $requisition->id)
                        ->where('id', $itemData['id'])
                        ->firstOrFail();

                    $item->update([
                        'product_id' => $itemData['product_id'],
                        'requested_quantity' => $itemData['requested_quantity'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);

                    $sentIds[] = $item->id;
                } else {
                    // Novo item
                    $newItem = RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'product_id' => $itemData['product_id'],
                        'requested_quantity' => $itemData['requested_quantity'],
                        'delivered_quantity' => 0,
                        'notes' => $itemData['notes'] ?? null,
                        'item_status' => 'pendente',
                        'rejection_reason' => null,
                    ]);
                    $sentIds[] = $newItem->id;
                }
            }

            // Remove itens que não vieram mais no formulário (opcional)
            $toDelete = array_diff($existingIds, $sentIds);
            if (!empty($toDelete)) {
                RequisitionItem::whereIn('id', $toDelete)->delete();
            }
        });

        return redirect()
            ->route('almoxarife.requisitions.show', $requisition->id)
            ->with('success', 'Requisição atualizada com sucesso.');
    }

    /**
     * Formulário para receber produtos (quando requisição está aprovada/parcial)
     */
    public function receiveForm($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with(['items.product', 'client'])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        // Só pode receber se o status permitir
        if (!in_array($requisition->status, ['aprovado', 'parcial', 'em curso'])) {
            return redirect()
                ->route('almoxarife.requisitions.show', $requisition->id)
                ->with('warning', 'Esta requisição não está em estado que permita recebimento de produtos.');
        }

        return view('almoxarife.requisitions.receive', compact('requisition', 'company'));
    }

    /**
     * Processa o recebimento dos produtos de uma requisição.
     */
    public function receiveStore(Request $request, $id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with('items.product')
            ->where('company_id', $company->id)
            ->findOrFail($id);

        if (!in_array($requisition->status, ['aprovado', 'parcial', 'em curso'])) {
            return redirect()
                ->route('almoxarife.requisitions.show', $requisition->id)
                ->with('warning', 'Esta requisição não está em estado que permita recebimento de produtos.');
        }

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:requisition_items,id'],
            'items.*.received_now' => ['nullable', 'integer', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'], // tipo da entrada, ex: "requisição"
        ]);

        DB::transaction(function () use ($data, $requisition, $user, $company) {

            $itemsInput = $data['items'];
            $supplier = $data['supplier'] ?? null;
            $entryType = $data['type'] ?? 'requisição';

            foreach ($itemsInput as $itemInput) {
                /** @var \App\Models\RequisitionItem $item */
                $item = $requisition->items->firstWhere('id', (int) $itemInput['id']);
                if (!$item) {
                    continue; // segurança
                }

                // Se item_status for concluido ou rejeitado, não deve receber nada
                if (in_array($item->item_status, ['concluido', 'rejeitado'])) {
                    continue;
                }

                $receivedNow = isset($itemInput['received_now'])
                    ? (int) $itemInput['received_now']
                    : 0;

                if ($receivedNow <= 0) {
                    // Nada a receber para este item
                    continue;
                }

                $restante = $item->requested_quantity - $item->delivered_quantity;

                // Garantir que não ultrapassa o restante
                if ($receivedNow > $restante) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.{$item->id}.received_now" => "A quantidade a receber para o produto {$item->product->name} não pode ser superior à quantidade restante ({$restante}).",
                    ]);
                }

                // Atualizar delivered_quantity
                $item->delivered_quantity += $receivedNow;

                // Atualizar notas do item, se foi enviado algo
                if (!empty($itemInput['notes'])) {
                    $item->notes = $itemInput['notes'];
                }

                // Recalcular item_status
                if ($item->delivered_quantity == 0) {
                    if ($item->item_status === 'pendente') {
                        $item->item_status = 'aprovado'; // ou manter pendente, conforme tua regra
                    }
                } elseif ($item->delivered_quantity < $item->requested_quantity) {
                    $item->item_status = 'em curso';
                } else { // delivered == requested
                    $item->item_status = 'concluido';
                }

                $item->save();

                // ======== DEFINIR ENTIDADE CORRETA (EMPRESA OU CLIENTE) ========

                if (is_null($requisition->client_id)) {
                    // Requisição interna: cliente é a própria empresa
                    $entityType = 'company';
                    $entityId = $requisition->company_id;
                } else {
                    // Requisição para cliente da empresa
                    $entityType = 'client';
                    $entityId = $requisition->client_id;
                }

                // Buscar ou criar EntityProduct PARA ESSA ENTIDADE (company ou client)
                $entityProduct = EntityProduct::where('company_id', $company->id)
                    ->where('entity_type', $entityType)
                    ->where('entity_id', $entityId)
                    ->where('product_id', $item->product_id)
                    ->first();

                if (!$entityProduct) {
                    $entityProduct = EntityProduct::create([
                        'company_id' => $company->id,
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'product_id' => $item->product_id,
                        'quantity' => 0,
                        'requested_quantity' => null,
                        'min_stock' => null,
                        'max_stock' => null,
                        'status' => null,
                    ]);
                }

                // Incrementar estoque da entidade correta
                $entityProduct->quantity += $receivedNow;
                $entityProduct->save();

                // Criar um registro em EntryProduct para ESSA entidade
                EntryProduct::create([
                    'user_id' => $user->id,
                    'entity_product_id' => $entityProduct->id,
                    'supplier' => $supplier,
                    'type' => $entryType,
                    'quantity' => $receivedNow,
                    'notes' => 'Entrada via requisição #' . $requisition->code,
                ]);
            }

            // ===== Recalcular status geral da requisição =====
            $requisition->load('items');

            $itemsNaoRejeitados = $requisition->items->where('item_status', '!=', 'rejeitado');

            $todosConcluidos = $itemsNaoRejeitados->count() > 0 &&
                $itemsNaoRejeitados->every(function ($it) {
                    return $it->item_status === 'concluido';
                });

            $algumEmCursoOuComEntrega = $itemsNaoRejeitados->contains(function ($it) {
                return in_array($it->item_status, ['em curso'])
                    || ($it->item_status === 'aprovado' && $it->delivered_quantity > 0);
            });

            if ($todosConcluidos) {
                $requisition->status = 'concluido';
            } elseif ($algumEmCursoOuComEntrega) {
                $requisition->status = 'em curso';
            } else {
                // Mantém o status atual (aprovado, parcial, etc.)
            }

            $requisition->save();
        });

        return redirect()
            ->route('almoxarife.requisitions.show', $requisition->id)
            ->with('success', 'Recebimento de produtos registrado com sucesso e estoque actualizado.');
    }

    /**
     * Define o status do item com base nas quantidades
     *
     * - 0 entregue => 'pendente'
     * - >0 e < solicitado => 'parcial'
     * - == solicitado => 'concluido'
     */
    protected function computeItemStatus(int $requested, int $delivered): string
    {
        if ($requested <= 0) {
            // Se por algum motivo não há solicitado, tratamos como concluído
            return 'concluido';
        }

        if ($delivered <= 0) {
            return 'pendente';
        }

        if ($delivered < $requested) {
            return 'parcial';
        }

        // delivered == requested ou maior (já garantimos no recebimento)
        return 'concluido';
    }

    /**
     * Recalcula o status da requisição com base nos itens
     *
     * - todos 'concluido' => 'concluida'
     * - pelo menos 1 com delivered > 0 e algum ainda não concluído => 'parcial'
     * - nenhum entregue ainda, mas status era 'aprovada' => mantemos 'aprovada'
     */
    protected function recomputeRequisitionStatus(Requisition $requisition): void
    {
        $requisition->load('items');

        $items = $requisition->items;

        if ($items->isEmpty()) {
            // Se não tiver item, mantemos o status atual
            return;
        }

        $allConcluded = $items->every(function (RequisitionItem $item) {
            return $item->item_status === 'concluido';
        });

        $anyDelivered = $items->contains(function (RequisitionItem $item) {
            return ($item->delivered_quantity ?? 0) > 0;
        });

        if ($allConcluded) {
            $requisition->status = 'concluida';
        } elseif ($anyDelivered) {
            $requisition->status = 'parcial';
        } else {
            // Nenhum entregue ainda => mantemos 'aprovada' (ou o que já estava)
            // Se quiser forçar:
            if ($requisition->status === 'aprovada' || $requisition->status === 'parcial') {
                $requisition->status = 'aprovada';
            }
        }

        $requisition->save();
    }

    public function exportPdf(Request $request, $id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with([
            'items.product.unit',
            'client',
            'user',
            'company.group', // <— importante
        ])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        $company = $requisition->company;          // já com ->group carregado
        $group = $company->group ?? null;        // dados vindos de company_groups

        $data = [
            'requisition' => $requisition,
            'company' => $company,
            'group' => $group,
            'client' => $requisition->client,
        ];

        $pdf = Pdf::loadView('almoxarife.requisitions.print', $data)
            ->setPaper('a4', 'landscape');

        $filename = "REQUISICAO_{$requisition->code}_{$requisition->id}.pdf";

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request, $id)
    {
        $user = auth()->user();
        $company = $user->company;

        $requisition = Requisition::with([
            'items.product.unit',
            'client',
            'user',
            'company.group', // importante
        ])
            ->where('company_id', $company->id)
            ->findOrFail($id);

        return Excel::download(
            new RequisitionExport($requisition),
            "REQUISICAO_{$requisition->code}_{$requisition->id}.xlsx"
        );
    }
}
