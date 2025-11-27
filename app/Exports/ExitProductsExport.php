<?php

namespace App\Exports;

use App\Models\ExitProduct;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExitProductsExport implements FromView
{
    protected $filters;
    protected $companyId;
    protected $userId;

    public function __construct($companyId, $userId, array $filters = [])
    {
        $this->companyId = $companyId;
        $this->userId    = $userId;
        $this->filters   = $filters;
    }

    public function view(): View
    {
        $query = ExitProduct::with(['entityProduct.product', 'entityProduct.entity'])
            ->whereHas('entityProduct', function ($q) {
                $q->where('company_id', $this->companyId);
            })
            ->orderBy('id', 'desc');

        // Filtros
        if (!empty($this->filters['type'])) {
            $query->where('type', 'like', '%' . $this->filters['type'] . '%');
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $exits = $query->get();

        return view('almoxarife.exit_products.reports.excel', [
            'exits' => $exits,
        ]);
    }
}
