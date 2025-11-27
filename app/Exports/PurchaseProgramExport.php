<?php

namespace App\Exports;

use App\Models\PurchaseProgram;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseProgramExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $program;

    public function __construct(PurchaseProgram $program)
    {
        $this->program = $program->load([
            'requisition.client',
            'requisition.user',
            'items.product.unit',
            'items.requisitionItem',
        ]);
    }

    public function title(): string
    {
        return $this->program->code;
    }

    public function headings(): array
    {
        return [
            'Item',
            'Requisição',
            'Cod. Produto',
            'Descrição',
            'Prioridade',
            'Unidade',
            'Quantidade',
            'Fornecedor',
            'Setor Solicitante',
            'Solicitante',
            'Finalidade',
            'Forma Pagamento',
            'Valor Orçado (Unit.)',
            'Valor Total (AKZ)',
            'Status',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $methodTotals = [];
        $grandTotal = 0;

        foreach ($this->program->items as $index => $item) {
            $reqItem  = $item->requisitionItem;
            $product  = $item->product;
            $qty      = $reqItem->requested_quantity ?? 0;
            $unit     = $item->budget_unit_value ?? 0;
            $total    = $item->budget_total_value ?? ($qty * $unit);
            $priority = strtoupper($this->program->requisition->priority ?? '-');
            $itemStatusLabel = $item->status ?? '-';

            $method   = trim(strtoupper($item->payment_method ?? 'SEM DEFINIÇÃO'));

            if (!isset($methodTotals[$method])) {
                $methodTotals[$method] = 0;
            }
            $methodTotals[$method] += $total;
            $grandTotal += $total;

            $rows[] = [
                $index + 1,
                $this->program->requisition->code,
                $product?->code,
                $product?->name,
                $priority,
                $product?->unit?->symbol ?? '',
                $qty,
                $item->supplier_name,
                $this->program->requisition->department ?? '',
                $this->program->requisition->user?->name ?? '',
                $this->program->requisition->purpose ?? '',
                $method,
                $unit,
                $total,
                $itemStatusLabel,
            ];
        }

        // Linha em branco
        $rows[] = [];

        // Cabeçalho dos totais por método
        $rows[] = ['Totais por forma de pagamento', '', '', '', '', '', '', '', '', '', '', '', '', ''];

        foreach ($methodTotals as $method => $total) {
            $rows[] = [
                $method,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $total,
            ];
        }

        // Total geral
        $rows[] = [
            'TOTAL GERAL',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $grandTotal,
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // cabeçalhos em negrito
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        return [];
    }
}
