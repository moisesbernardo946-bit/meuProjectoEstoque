<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <title>Programação - {{ $program->code }}</title>
    <style>
        /* DOMPDF-friendly CSS: evite flexbox/modern CSS */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 8px;
        }

        .sheet {
            width: 100%;
        }

        /* cabeçalho: tabela com 3 colunas */
        .hdr-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .hdr-left {
            width: 180px;
            vertical-align: top;
            padding: 6px;
        }

        .hdr-center {
            vertical-align: top;
            padding: 6px;
        }

        .hdr-right {
            width: 240px;
            vertical-align: top;
            padding: 6px;
            text-align: right;
            font-size: 12px;
        }

        .logo-block {
            width: 150px;
            height: 85px;
            background: #b61a1a;
            color: #fff;
            display: block;
            text-align: center;
            line-height: 85px;
            font-weight: bold;
            font-size: 28px;
        }

        .company-info b {
            display: block;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .company-info small {
            display: block;
            color: #333;
        }

        /* tabela principal */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 11px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 6px 6px;
            vertical-align: middle;
        }

        .main-table th {
            font-weight: 700;
            background: #fff;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: 11px;
            color: #444;
        }

        /* box de totais (direita) */
        .totals {
            width: 320px;
            margin-top: 10px;
            border: 1px solid #000;
            float: right;
            font-size: 11px;
        }

        .totals .row {
            padding: 6px 8px;
            border-bottom: 1px solid #000;
            display: table;
            width: 100%;
        }

        .totals .row .k {
            display: table-cell;
        }

        .totals .row .v {
            display: table-cell;
            text-align: right;
            width: 120px;
        }

        .totals .row.total {
            font-weight: 700;
            border-top: 2px solid #000;
        }

        /* assinaturas */
        .signatures {
            margin-top: 70px;
            width: 100%;
        }

        .sig {
            width: 30%;
            display: inline-block;
            text-align: center;
        }

        .sig .line {
            border-top: 1px solid #000;
            margin-top: 36px;
            padding-top: 6px;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <table class="hdr-table">
            <tr>
                <td class="hdr-left">
                    <div class="logo-block">TERRA</div>
                </td>
                <td class="hdr-center company-info">
                    <b>{{ $company->name ?? 'Empresa' }}</b>
                    <small>{{ $company->address ?? '' }}</small>
                    <small>NIF: {{ $company->nif ?? '---' }}</small>
                    <small>Comprador: {{ $program->buyer_name }}</small>
                    <small>Tel: {{ $program->buyer_phone ?? ($user->phone ?? '---') }}</small>
                    <small>Email: {{ $program->buyer_email ?? ($user->email ?? '---') }}</small>
                </td>
                <td class="hdr-right">
                    DATA: <b>{{ optional($program->created_at)->format('d/m/Y') }}</b><br>
                    Nº: <b>{{ $program->code }}</b><br>
                    EMPRESA: <b>{{ $company->code ?? '' }}</b>
                    <div>
                        STATUS: <b>{{ strtoupper($program->status) }}</b>
                    </div>
                </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Req.</th>
                    <th>Ped. Cod.</th>
                    <th>Prioridade</th>
                    <th>Descrição do Material</th>
                    <th>U.M.</th>
                    <th class="text-right">Qtd.</th>
                    <th>Fornecedor</th>
                    <th>Setor</th>
                    <th>Solicitante</th>
                    <th>Finalidade</th>
                    <th>C. PGTO</th>
                    <th class="text-right">Valor Orçado</th>
                    <th class="text-right">Valor Total (AKZ)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($program->items as $i => $item)
                    @php
                        $reqItem = $item->requisitionItem;
                        $product = $item->product;
                        $qty = $reqItem->requested_quantity ?? 0;
                        $unit = $item->budget_unit_value ?? 0;
                        $total = $item->budget_total_value ?? $qty * $unit;
                        $itemStatusLabel = $item->status ?? '-';
                    @endphp
                    <tr>
                        <td>{{ sprintf('%02d', $i + 1) }}</td>
                        <td>{{ $program->requisition->code }}</td>
                        <td>{{ $product?->code ?? '' }}</td>
                        <td>{{ strtoupper($program->requisition->priority ?? '-') }}</td>
                        <td>{{ $product?->name ?? '' }}</td>
                        <td>{{ $product?->unit?->symbol ?? '' }}</td>
                        <td class="text-right">{{ number_format($qty, 2, ',', '.') }}</td>
                        <td>{{ $item->supplier_name ?? '' }}</td>
                        <td>{{ $program->requisition->department ?? '' }}</td>
                        <td>{{ $program->requisition->user?->name ?? '' }}</td>
                        <td>{{ $program->requisition->purpose ?? '' }}</td>
                        <td>{{ strtoupper($item->payment_method ?? '---') }}</td>
                        <td class="text-right">{{ number_format($unit, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total, 2, ',', '.') }}</td>
                        <td>{{ strtoupper($itemStatusLabel) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            if (!isset($methodTotals) || !is_array($methodTotals)) {
                $methodTotals = [];
                $grandTotal = 0;
                foreach ($program->items as $it) {
                    $m = trim(strtoupper($it->payment_method ?? 'SEM DEFINIÇÃO'));
                    $v = $it->budget_total_value ?? 0;
                    $methodTotals[$m] = ($methodTotals[$m] ?? 0) + $v;
                    $grandTotal += $v;
                }
            }
        @endphp

        <div class="totals">
            @foreach ($methodTotals as $m => $v)
                <div class="row">
                    <div class="k">{{ 'Valor Compras ' . $m . ' (AKZ):' }}</div>
                    <div class="v">{{ number_format($v, 2, ',', '.') }}</div>
                </div>
            @endforeach
            <div class="row total">
                <div class="k">TOTAL GERAL</div>
                <div class="v">{{ number_format($grandTotal ?? 0, 2, ',', '.') }}</div>
            </div>
        </div>

        <div style="clear:both"></div>

        <div class="signatures">
            <div class="sig">
                <div class="line"></div>
                <div>Gestor Suprimentos</div>
            </div>
            <div class="sig">
                <div class="line"></div>
                <div>Gestor Administrativo</div>
            </div>
            <div class="sig">
                <div class="line"></div>
                <div>Financeiro</div>
            </div>
        </div>
    </div>
</body>

</html>
