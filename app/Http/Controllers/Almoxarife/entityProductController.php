<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\EntityProduct;
use App\Models\Product;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntityProductController extends Controller
{
    /**
     * Calcula o status dinâmico de um EntityProduct
     * (sem usar coluna no banco).
     */
    private function computeStatus(EntityProduct $ep): string
    {
        // Empresa: usa quantity, min_stock, max_stock
        if ($ep->entity_type === 'company') {
            $q   = (int) ($ep->quantity ?? 0);
            $min = $ep->min_stock !== null ? (int) $ep->min_stock : null;
            $max = $ep->max_stock !== null ? (int) $ep->max_stock : null;

            if ($min === null || $max === null) {
                // Sem faixa definida -> podemos tratar como "normal" ou "sem-faixa"
                return 'normal';
            }

            if ($q < $min) {
                return 'critico';
            }

            if ($q > $max) {
                return 'excesso';
            }

            return 'normal';
        }

        // Cliente: usa quantity, requested_quantity
        if ($ep->entity_type === 'client') {
            $q   = (int) ($ep->quantity ?? 0);
            $req = $ep->requested_quantity !== null ? (int) $ep->requested_quantity : 0;

            if ($req <= 0) {
                // sem quantidade solicitada definida
                return $q > 0 ? 'concluido' : 'vazio';
            }

            if ($q === 0) {
                return 'vazio';
            }

            if ($q < $req) {
                return 'faltando';
            }

            if ($q > $req) {
                return 'excesso';
            }

            return 'concluido';
        }

        // fallback
        return 'desconhecido';
    }

    public function index(Request $request)
    {
        $user    = auth()->user();
        $company = $user->company;

        $search     = $request->get('search');
        $entityType = $request->get('entity_type'); // company | client | null
        $entityId   = $request->get('entity_id');
        $status     = $request->get('status');      // critico, normal, excesso, vazio, faltando, concluido

        $query = EntityProduct::with(['product', 'company', 'entity'])
            ->where('company_id', $company->id);

        if ($entityType) {
            $query->where('entity_type', $entityType);
        }

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Carrega registros da base (sem filtro por status ainda)
        $entityProducts = $query->orderBy('id', 'desc')->get();

        // Calcula status dinâmico para cada item
        $entityProducts->transform(function (EntityProduct $ep) {
            $ep->computed_status = $this->computeStatus($ep);
            return $ep;
        });

        // Filtro em memória por status, se informado
        if ($status) {
            $entityProducts = $entityProducts->filter(function (EntityProduct $ep) use ($status) {
                return $ep->computed_status === $status;
            });
        }

        // Paginação manual (coleção) – 15 por página
        $perPage   = 15;
        $page      = (int) ($request->get('page', 1));
        $total     = $entityProducts->count();
        $itemsPage = $entityProducts->forPage($page, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsPage,
            $total,
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        // entidades disponíveis (dessa empresa)
        $clients  = Client::where('company_id', $company->id)->orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('almoxarife.entity_products.index', [
            'entityProducts' => $paginator,
            'clients'        => $clients,
            'products'       => $products,
            'search'         => $search,
            'entityType'     => $entityType,
            'entityId'       => $entityId,
            'status'         => $status,
            'company'        => $company,
        ]);
    }

    public function create()
    {
        $user           = auth()->user();
        $currentCompany = $user->company; // empresa filha à qual o almoxarife pertence

        $clients = Client::where('company_id', $currentCompany->id)
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')->get();

        return view('almoxarife.entity_products.create', compact(
            'currentCompany',
            'clients',
            'products',
        ));
    }

    public function store(Request $request)
    {
        $user    = auth()->user();
        $company = $user->company;

        $request->validate([
            'entity_type'    => 'required|in:client,company',
            'entity_id'      => 'required|integer',
            'product_ids'    => 'required|array|min:1',
            'product_ids.*'  => 'required|integer|exists:products,id',

            // para cliente
            'requested_quantity'   => 'array',
            'requested_quantity.*' => 'nullable|integer|min:1',

            // para empresa
            'min_stock'   => 'array',
            'min_stock.*' => 'nullable|integer|min:0',
            'max_stock'   => 'array',
            'max_stock.*' => 'nullable|integer|min:0',
        ]);

        $entityType = $request->entity_type;
        $entityId   = $request->entity_id;
        $productIds = $request->product_ids;

        $createdCount = 0;
        $duplicated   = [];

        foreach ($productIds as $index => $productId) {
            $exists = EntityProduct::where('company_id', $company->id)
                ->where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('product_id', $productId)
                ->exists();

            if ($exists) {
                $product      = Product::find($productId);
                $duplicated[] = $product?->name ?? "ID {$productId}";
                continue;
            }

            $quantity          = 0; // padrão, controlado por entradas/saídas
            $requestedQuantity = $entityType === 'client'
                ? ($request->requested_quantity[$index] ?? null)
                : null;

            $minStock = $entityType === 'company'
                ? ($request->min_stock[$index] ?? null)
                : null;

            $maxStock = $entityType === 'company'
                ? ($request->max_stock[$index] ?? null)
                : null;

            // Validação min/max por item (empresa)
            if ($entityType === 'company' && $minStock !== null && $maxStock !== null && $minStock > $maxStock) {
                // apenas pula esse registro e registra como "duplicado lógico"
                $product      = Product::find($productId);
                $duplicated[] = ($product?->name ?? "ID {$productId}") . ' (min > max)';
                continue;
            }

            EntityProduct::create([
                'company_id'         => $company->id,
                'entity_type'        => $entityType,
                'entity_id'          => $entityId,
                'product_id'         => $productId,
                'quantity'           => $quantity,
                'requested_quantity' => $requestedQuantity,
                'min_stock'          => $minStock,
                'max_stock'          => $maxStock,
            ]);

            $createdCount++;
        }

        $messages = [];
        if ($createdCount > 0) {
            $messages[] = "✅ {$createdCount} produto(s) associados com sucesso.";
        }

        if (!empty($duplicated)) {
            $entityLabel = $entityType === 'client'
                ? Client::find($entityId)?->name
                : Company::find($entityId)?->name;

            $duplicatedList = implode(', ', $duplicated);
            $messages[]     = "⚠ Os produtos {$duplicatedList} não foram associados (já existiam ou min > max) para {$entityLabel}.";
        }

        return redirect()
            ->route('almoxarife.entity_products.index')
            ->with('success', implode(' ', $messages));
    }

    public function edit($id)
    {
        $user    = auth()->user();
        $company = $user->company;

        $entityProduct = EntityProduct::with('product')
            ->where('company_id', $company->id)
            ->findOrFail($id);

        $isClient  = $entityProduct->entity_type === 'client';
        $isCompany = $entityProduct->entity_type === 'company';

        $clients  = Client::where('company_id', $company->id)->orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('almoxarife.entity_products.edit', compact(
            'entityProduct',
            'company',
            'clients',
            'products',
            'isClient',
            'isCompany'
        ));
    }

    public function update(Request $request, $id)
    {
        $user    = auth()->user();
        $company = $user->company;

        $entityProduct = EntityProduct::where('company_id', $company->id)->findOrFail($id);

        $entityType = $entityProduct->entity_type;

        $baseRules = [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
        ];

        if ($entityType === 'client') {
            $rules = array_merge($baseRules, [
                'entity_id'          => ['required', 'integer', Rule::exists('clients', 'id')],
                'requested_quantity' => ['nullable', 'integer', 'min:1'],
            ]);
        } else {
            $rules = array_merge($baseRules, [
                'min_stock' => ['nullable', 'integer', 'min:0'],
                'max_stock' => ['nullable', 'integer', 'min:0'],
            ]);
        }

        $validated = $request->validate($rules);

        if ($entityType === 'client') {
            $client = Client::where('company_id', $company->id)
                ->where('id', $validated['entity_id'])
                ->first();

            if (!$client) {
                return back()
                    ->withErrors(['entity_id' => 'Cliente inválido para a sua empresa.'])
                    ->withInput();
            }

            $exists = EntityProduct::where('company_id', $company->id)
                ->where('entity_type', 'client')
                ->where('entity_id', $validated['entity_id'])
                ->where('product_id', $validated['product_id'])
                ->where('id', '!=', $entityProduct->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withErrors(['product_id' => 'Já existe uma associação desse produto para o cliente selecionado.'])
                    ->withInput();
            }

            $entityProduct->update([
                'product_id'         => $validated['product_id'],
                'entity_id'          => $validated['entity_id'],
                'requested_quantity' => $validated['requested_quantity'] ?? null,
                'min_stock'          => null,
                'max_stock'          => null,
            ]);
        } else {
            if ((int) $entityProduct->entity_id !== (int) $company->id) {
                abort(403, 'Não autorizado a editar associação de outra empresa.');
            }

            $min = $validated['min_stock'] ?? null;
            $max = $validated['max_stock'] ?? null;

            if ($min !== null && $max !== null && $min > $max) {
                return back()
                    ->withErrors(['min_stock' => 'O estoque mínimo não pode ser maior que o estoque máximo.'])
                    ->withInput();
            }

            $exists = EntityProduct::where('company_id', $company->id)
                ->where('entity_type', 'company')
                ->where('entity_id', $company->id)
                ->where('product_id', $validated['product_id'])
                ->where('id', '!=', $entityProduct->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withErrors(['product_id' => 'Já existe uma associação desse produto para a sua empresa.'])
                    ->withInput();
            }

            $entityProduct->update([
                'product_id'         => $validated['product_id'],
                'min_stock'          => $min,
                'max_stock'          => $max,
                'requested_quantity' => null,
            ]);
        }

        return redirect()
            ->route('almoxarife.entity_products.index')
            ->with('success', 'Associação actualizada com sucesso.');
    }

    public function show($id)
    {
        $user    = auth()->user();
        $company = $user->company;

        $entityProduct = EntityProduct::with([
            'product.category',
            'product.unit',
            'product.zone',
            'company',
            'entity',
        ])->where('company_id', $company->id)->findOrFail($id);

        // status dinâmico
        $entityProduct->computed_status = $this->computeStatus($entityProduct);

        return view('almoxarife.entity_products.show', compact('entityProduct', 'company'));
    }

    public function destroy($id)
    {
        $user    = auth()->user();
        $company = $user->company;

        $entityProduct = EntityProduct::where('company_id', $company->id)->findOrFail($id);
        $entityProduct->delete();

        return redirect()
            ->route('almoxarife.entity_products.index')
            ->with('success', '🗑 Associação removida com sucesso.');
    }
}
