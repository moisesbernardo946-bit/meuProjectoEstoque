<?php

namespace App\Exports;

use App\Models\EntryProduct;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class EntryProductsExport implements FromView
{
    /**
     * Filtros usados na exportação
     */
    protected $filters;
    protected $companyId;
    protected $userId;

    /**
     * Recebemos filtros (supplier, type, date_from, date_to) e contexto
     */
    public function __construct($companyId, $userId, array $filters = [])
    {
        $this->companyId = $companyId;
        $this->userId    = $userId;
        $this->filters   = $filters;
    }

    /**
     * Gera a view que será usada pelo Excel
     */
    public function view(): View
    {
        $query = EntryProduct::with(['entityProduct.product', 'entityProduct.entity'])
            ->whereHas('entityProduct', function ($q) {
                $q->where('company_id', $this->companyId);
            })
            ->orderBy('id', 'desc');

        // Filtros
        if (!empty($this->filters['supplier'])) {
            $query->where('supplier', 'like', '%' . $this->filters['supplier'] . '%');
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', 'like', '%' . $this->filters['type'] . '%');
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $entries = $query->get();

        return view('almoxarife.entry_products.reports.excel', [
            'entries' => $entries,
        ]);
    }
}
