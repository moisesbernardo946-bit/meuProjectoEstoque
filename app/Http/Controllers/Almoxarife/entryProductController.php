<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\EntryProduct;
use App\Models\EntityProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\EntryProductsExport;

class EntryProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $productSearch = $request->get('product');      // nome ou código
        $type = $request->get('type');         // tipo de entrada
        $dateStart = $request->get('date_start');
        $dateEnd = $request->get('date_end');

        $entries = EntryProduct::with(['entityProduct.product', 'entityProduct.entity'])
            ->whereHas('entityProduct', function ($q) use ($company, $productSearch) {
                $q->where('company_id', $company->id)
                    ->when($productSearch, function ($q2) use ($productSearch) {
                        $q2->whereHas('product', function ($qp) use ($productSearch) {
                            $qp->where('name', 'like', "%{$productSearch}%")
                                ->orWhere('code', 'like', "%{$productSearch}%");
                        });
                    });
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', 'like', "%{$type}%");
            })
            ->when($dateStart, function ($q) use ($dateStart) {
                $q->whereDate('created_at', '>=', $dateStart);
            })
            ->when($dateEnd, function ($q) use ($dateEnd) {
                $q->whereDate('created_at', '<=', $dateEnd);
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->appends($request->all());

        return view('almoxarife.entry_products.index', compact('entries'));
    }

    public function create()
    {
        $user = auth()->user();
        $company = $user->company;

        // EntityProducts da empresa do user, com produto carregado.
        $entityProducts = EntityProduct::with('product', 'entity')
            ->where('company_id', $company->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('almoxarife.entry_products.create', compact('entityProducts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $data = $request->validate([
            'entity_product_id' => ['required', 'integer', 'exists:entity_products,id'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        // Garantir que entity_product pertence à empresa do user
        $entityProduct = EntityProduct::with('product')
            ->where('company_id', $company->id)
            ->findOrFail($data['entity_product_id']);

        $alertMessage = null;

        DB::transaction(function () use ($data, $user, $entityProduct, &$alertMessage) {
            // Cria o registro de entrada
            EntryProduct::create([
                'user_id' => $user->id,
                'entity_product_id' => $entityProduct->id,
                'supplier' => $data['supplier'] ?? null,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Atualiza o estoque
            $entityProduct->increment('quantity', $data['quantity']);
            $entityProduct->refresh();

            // Verifica se gerou "excesso"
            if (!is_null($entityProduct->max_stock) && $entityProduct->quantity > $entityProduct->max_stock) {
                $produtoNome = $entityProduct->product?->name ?? 'Produto';
                $alertMessage = "⚠ O produto {$produtoNome} está em EXCESSO após esta entrada.";
            }
        });

        $msg = 'Entrada registrada e estoque atualizado com sucesso.';
        if ($alertMessage) {
            $msg .= " {$alertMessage}";
        }

        return redirect()
            ->route('almoxarife.entry_products.index')
            ->with('success', $msg);
    }

    public function show($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $entry = EntryProduct::with(['entityProduct.product', 'entityProduct.entity', 'user'])
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        return view('almoxarife.entry_products.show', compact('entry'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $entry = EntryProduct::with('entityProduct.product', 'entityProduct.entity')
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        // Para edição, não vamos deixar trocar o entity_product (para não bagunçar a lógica de estoque).
        // Mas vamos permitir editar tipo e quantidade.
        return view('almoxarife.entry_products.edit', compact('entry'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $company = $user->company;

        $entry = EntryProduct::with('entityProduct.product')
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        $data = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $alertMessage = null;

        DB::transaction(function () use ($data, $entry, &$alertMessage) {
            $entityProduct = $entry->entityProduct;

            // Primeiro, desfazemos o efeito da entrada antiga
            if ($entityProduct->quantity < $entry->quantity) {
                abort(400, 'Não é possível atualizar esta entrada, pois o ajuste deixaria o estoque negativo.');
            }
            $entityProduct->decrement('quantity', $entry->quantity);

            // Depois aplicamos a nova quantidade
            $entityProduct->increment('quantity', $data['quantity']);
            $entityProduct->refresh();

            // Atualiza a entrada
            $entry->update([
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'supplier' => $data['supplier'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Verifica excesso novamente
            if (!is_null($entityProduct->max_stock) && $entityProduct->quantity > $entityProduct->max_stock) {
                $produtoNome = $entityProduct->product?->name ?? 'Produto';
                $alertMessage = "⚠ O produto {$produtoNome} está em EXCESSO após esta atualização.";
            }
        });

        $msg = 'Entrada atualizada e estoque ajustado com sucesso.';
        if ($alertMessage) {
            $msg .= " {$alertMessage}";
        }

        return redirect()
            ->route('almoxarife.entry_products.index')
            ->with('success', $msg);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $entry = EntryProduct::with('entityProduct')
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        DB::transaction(function () use ($entry) {
            $entityProduct = $entry->entityProduct;

            if ($entityProduct->quantity < $entry->quantity) {
                abort(400, 'Não é possível excluir esta entrada, pois deixaria o estoque negativo.');
            }

            $entityProduct->decrement('quantity', $entry->quantity);
            $entry->delete();
        });

        return redirect()
            ->route('almoxarife.entry_products.index')
            ->with('success', 'Entrada excluída e estoque ajustado.');
    }

    /** EXPORT PDF */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $productSearch = $request->get('product');
        $type = $request->get('type');
        $dateStart = $request->get('date_start');
        $dateEnd = $request->get('date_end');

        $entries = EntryProduct::with(['entityProduct.product', 'entityProduct.entity', 'user'])
            ->whereHas('entityProduct', function ($q) use ($company, $productSearch) {
                $q->where('company_id', $company->id)
                    ->when($productSearch, function ($q2) use ($productSearch) {
                        $q2->whereHas('product', function ($qp) use ($productSearch) {
                            $qp->where('name', 'like', "%{$productSearch}%")
                                ->orWhere('code', 'like', "%{$productSearch}%");
                        });
                    });
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', 'like', "%{$type}%");
            })
            ->when($dateStart, function ($q) use ($dateStart) {
                $q->whereDate('created_at', '>=', $dateStart);
            })
            ->when($dateEnd, function ($q) use ($dateEnd) {
                $q->whereDate('created_at', '<=', $dateEnd);
            })
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView('almoxarife.entry_products.reports.pdf', [
            'entries' => $entries,
            'company' => $company,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('entradas_produtos.pdf');
    }

    /** EXPORT EXCEL */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $filters = [
            'supplier' => $request->get('supplier'),
            'type' => $request->get('type'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        return Excel::download(
            new EntryProductsExport($company->id, $user->id, $filters),
            'entradas_produtos_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
