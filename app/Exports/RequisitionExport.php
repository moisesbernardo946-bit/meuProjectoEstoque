<?php

namespace App\Exports;

use App\Models\Requisition;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RequisitionExport implements FromCollection, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell, WithTitle
{
    protected Requisition $requisition;

    public function __construct(Requisition $requisition)
    {
        $this->requisition = $requisition;
    }

    public function collection()
    {
        return $this->requisition
            ->items()
            ->with('product.unit')
            ->get();
    }

    public function startCell(): string
    {
        // Cabeçalho ocupa até linha 10/11, então a tabela começa em 12
        return 'A12';
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->product->name ?? '—',
            $this->requisition->purpose ?? '-',
            $item->requested_quantity,
            $item->product->unit->symbol ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $req     = $this->requisition;
        $company = $req->company;
        $group   = $company->group ?? null;

        // Helper rápido pra borda fina
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        // =========================
        // TÍTULO PRINCIPAL
        // =========================
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'REQUISIÇÃO DE COMPRA DE MATERIAIS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // =========================
        // BLOCO PRINCIPAL (LINHA 2 ATÉ 7)
        // A:B -> LOGO
        // C:D -> EMPRESA
        // E:F -> BLOCO DIREITA
        // =========================

        // --- LOGO (A2:B7) ---
        // Mescla colunas A:B em cada linha do bloco para ficar como um "quadrado" à esquerda
        foreach (range(2, 7) as $row) {
            $sheet->mergeCells("A{$row}:B{$row}");
        }

        $sheet->setCellValue('A2', 'GRUPO');
        $sheet->setCellValue('A3', 'TERRA');
        $sheet->setCellValue('A4', 'SOLUÇÕES COMPLETAS EM');
        $sheet->setCellValue('A5', 'CONSTRUÇÃO E AMBIENTES');
        // linhas 6 e 7 podem ficar vazias só para manter proporção

        $sheet->getStyle('A2:A7')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // --- EMPRESA (C2:D7) ---
        foreach (range(2, 7) as $row) {
            $sheet->mergeCells("C{$row}:D{$row}");
        }

        $sheet->setCellValue('C2', 'GRUPO TERRA');
        $sheet->setCellValue('C3', 'Centro de Produção e Logística');
        $sheet->setCellValue('C4', 'End: ' . ($group->address ?? '—'));
        $sheet->setCellValue('C5', 'República de Angola');
        $sheet->setCellValue('C6', 'NIF: ' . ($group->nif ?? '—'));
        $sheet->setCellValue('C7', 'Telefone: ' . ($group->phone ?? '—'));

        $sheet->getStyle('C2:C7')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // --- BLOCO DIREITA (E2:F7) ---
        foreach (range(2, 7) as $row) {
            $sheet->mergeCells("E{$row}:F{$row}");
        }

        $sheet->setCellValue('E2', 'RCM ' . $req->code . '/' . now()->year);
        $sheet->setCellValue('E3', 'DATA: ' . $req->created_at->format('d-m-Y'));
        $sheet->setCellValue('E4', 'NIVEL DE PRIORIDADE: ' . strtoupper($req->priority ?? '-'));
        $sheet->setCellValue(
            'E5',
            'REQUERENTE: ' . (
                $req->requester_name
                ?? ($req->user->name ?? '-')
            )
        );
        // E6/E7 podem ficar vazias para equilíbrio visual

        $sheet->getStyle('E2:E7')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Bordas em volta do bloco inteiro (A2:F7)
        $sheet->getStyle('A2:F7')->applyFromArray($thinBorder);

        // =========================
        // LINHAS CLIENTE / NIF / ALMOXERIFE / EMPRESA (8 e 9)
        // =========================

        // Linha 8
        $sheet->mergeCells('A8:C8');
        $sheet->mergeCells('D8:F8');

        $sheet->setCellValue('A8', 'CLIENTE: ' . ($client?->name ?? $company->name ?? '-'));
        $sheet->setCellValue('D8', 'NIF: ' . ($group->nif ?? '—'));

        // Linha 9
        $sheet->mergeCells('A9:C9');
        $sheet->mergeCells('D9:F9');

        $sheet->setCellValue(
            'A9',
            'EMPRESA: ' . ($group->name ?? 'TERRA INTERIOR') . ' - ' . ($company->name ?? 'INDUSTRIA')
        );
        $sheet->setCellValue(
            'D9',
            'ALMOXERIFE: ' . ($req->user->name ?? '—')
        );

        $sheet->getStyle('A8:F9')->applyFromArray($thinBorder);
        $sheet->getStyle('A8:F9')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Pequeno espaço visual
        // (linha 10 pode ficar vazia)

        // =========================
        // CABEÇALHO DA TABELA (LINHA 12)
        // =========================

        $sheet->setCellValue('A12', 'Item');
        $sheet->setCellValue('B12', 'Descrição do Material');
        $sheet->setCellValue('C12', 'Finalidade');
        $sheet->setCellValue('D12', 'Qtd. Pedida');
        $sheet->setCellValue('E12', 'Und.');

        $sheet->getStyle('A12:E12')->getFont()->setBold(true);
        $sheet->getStyle('A12:E12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A12:E12')->applyFromArray($thinBorder);

        // =========================
        // BORDAS DA TABELA DE ITENS
        // =========================

        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 12) {
            $sheet->getStyle("A13:E{$lastRow}")->applyFromArray($thinBorder);
        }

        // =========================
        // ASSINATURAS (DEPOIS DA TABELA)
        // =========================

        $signatureRow = $lastRow + 3;

        // Requerente (A-C)
        $sheet->mergeCells("A{$signatureRow}:C{$signatureRow}");
        $sheet->setCellValue("A{$signatureRow}", 'O REQUERENTE');
        $sheet->getStyle("A{$signatureRow}:C{$signatureRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Gestor Compras (D-F)
        $sheet->mergeCells("D{$signatureRow}:F{$signatureRow}");
        $sheet->setCellValue("D{$signatureRow}", 'GESTOR COMPRAS');
        $sheet->getStyle("D{$signatureRow}:F{$signatureRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Linha de assinatura (borda superior) logo abaixo
        $lineRow = $signatureRow - 1;

        $sheet->mergeCells("A{$lineRow}:C{$lineRow}");
        $sheet->mergeCells("D{$lineRow}:F{$lineRow}");

        $sheet->getStyle("A{$lineRow}:C{$lineRow}")->getBorders()
            ->getTop()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("D{$lineRow}:F{$lineRow}")->getBorders()
            ->getTop()->setBorderStyle(Border::BORDER_THIN);

        // Ajustes de alinhamento geral
        $sheet->getStyle('A2:F7')->getAlignment()->setWrapText(true);

        // Opcional: ajustar largura das colunas para ficar mais parecido com o PDF
        $sheet->getColumnDimension('A')->setWidth(14); // Logo
        $sheet->getColumnDimension('B')->setWidth(4);  // (mesclado com A)
        $sheet->getColumnDimension('C')->setWidth(22); // Empresa
        $sheet->getColumnDimension('D')->setWidth(4);  // (mesclado com C)
        $sheet->getColumnDimension('E')->setWidth(22); // Bloco direita
        $sheet->getColumnDimension('F')->setWidth(4);  // (mesclado com E)
    }

    public function title(): string
    {
        return 'Requisição #' . $this->requisition->id;
    }
}
