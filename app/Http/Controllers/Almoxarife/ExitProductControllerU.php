<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\ExitProduct;
use App\Models\EntityProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ExitProductsExport;

class ExitProductControllerU extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $productSearch = $request->get('product');      // nome ou código
        $type = $request->get('type');         // tipo de saída
        $dateStart = $request->get('date_start');
        $dateEnd = $request->get('date_end');

        $exits = ExitProduct::with(['entityProduct.product', 'entityProduct.entity'])
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

        return view('almoxarife.exit_products.index', compact('exits'));
    }

    public function create()
    {
        $user = auth()->user();
        $company = $user->company;

        $entityProducts = EntityProduct::with('product', 'entity')
            ->where('company_id', $company->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('almoxarife.exit_products.create', compact('entityProducts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $data = $request->validate([
            'entity_product_id' => ['required', 'integer', 'exists:entity_products,id'],
            'type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $entityProduct = EntityProduct::with('product')
            ->where('company_id', $company->id)
            ->findOrFail($data['entity_product_id']);

        if ($entityProduct->quantity < $data['quantity']) {
            return back()
                ->withErrors(['quantity' => 'Quantidade informada é maior do que o estoque disponível.'])
                ->withInput();
        }

        $alertMessage = null;

        DB::transaction(function () use ($data, $user, $entityProduct, &$alertMessage) {
            // Cria o registro de saída
            ExitProduct::create([
                'user_id' => $user->id,
                'entity_product_id' => $entityProduct->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Atualiza o estoque
            $entityProduct->decrement('quantity', $data['quantity']);
            $entityProduct->refresh();

            // Verifica se ficou crítico
            if (!is_null($entityProduct->min_stock) && $entityProduct->quantity < $entityProduct->min_stock) {
                $produtoNome = $entityProduct->product?->name ?? 'Produto';
                $alertMessage = "⚠ O produto {$produtoNome} está em ESTADO CRÍTICO após esta saída.";
            }
        });

        $msg = 'Saída registrada e estoque atualizado com sucesso.';
        if ($alertMessage) {
            $msg .= " {$alertMessage}";
        }

        return redirect()
            ->route('almoxarife.exit_products.index')
            ->with('success', $msg);
    }

    public function show($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $exit = ExitProduct::with(['entityProduct.product', 'entityProduct.entity', 'user'])
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        return view('almoxarife.exit_products.show', compact('exit'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $exit = ExitProduct::with('entityProduct.product', 'entityProduct.entity')
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        return view('almoxarife.exit_products.edit', compact('exit'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $company = $user->company;

        $exit = ExitProduct::with('entityProduct.product')
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        $data = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $alertMessage = null;

        DB::transaction(function () use ($data, $exit, &$alertMessage) {
            $entityProduct = $exit->entityProduct;

            // desfazemos a saída antiga (devolvemos ao estoque)
            $entityProduct->increment('quantity', $exit->quantity);

            // agora tentamos aplicar a nova saída
            if ($entityProduct->quantity < $data['quantity']) {
                abort(400, 'Quantidade informada é maior do que o estoque disponível para atualizar esta saída.');
            }

            $entityProduct->decrement('quantity', $data['quantity']);
            $entityProduct->refresh();

            // atualiza a saída
            $exit->update([
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? null,
            ]);

            // checa crítico novamente
            if (!is_null($entityProduct->min_stock) && $entityProduct->quantity < $entityProduct->min_stock) {
                $produtoNome = $entityProduct->product?->name ?? 'Produto';
                $alertMessage = "⚠ O produto {$produtoNome} está em ESTADO CRÍTICO após esta atualização de saída.";
            }
        });

        $msg = 'Saída atualizada e estoque ajustado com sucesso.';
        if ($alertMessage) {
            $msg .= " {$alertMessage}";
        }

        return redirect()
            ->route('almoxarife.exit_products.index')
            ->with('success', $msg);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $company = $user->company;

        $exit = ExitProduct::with('entityProduct')
            ->whereHas('entityProduct', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->findOrFail($id);

        DB::transaction(function () use ($exit) {
            $entityProduct = $exit->entityProduct;

            // ao remover a saída, devolvemos ao estoque
            $entityProduct->increment('quantity', $exit->quantity);
            $exit->delete();
        });

        return redirect()
            ->route('almoxarife.exit_products.index')
            ->with('success', 'Saída excluída e estoque ajustado.');
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

        $exits = ExitProduct::with(['entityProduct.product', 'entityProduct.entity', 'user'])
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

        $pdf = Pdf::loadView('almoxarife.exit_products.reports.pdf', [
            'exits' => $exits,
            'company' => $company,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('saidas_produtos.pdf');
    }

    /** EXPORT EXCEL */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $filters = [
            'type' => $request->get('type'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        return Excel::download(
            new ExitProductsExport($company->id, $user->id, $filters),
            'saidas_produtos_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
