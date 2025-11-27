<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisição - {{ $requisition->code }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1124px;
            margin: auto;
            border: 1px solid #000;
            padding: 0;
        }

        /* Título */
        .title {
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 6px 0;
        }

        /* Blocos em linha */
        .table-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }

        .table-cell {
            display: table-cell;
            vertical-align: top;
            padding: 0;
        }

        .logo-block {
            min-height: 110px;
            text-align: center;
            vertical-align: middle;
        }

        /* Bordas e padding */
        .border-right { border-right: 1px solid #000; }
        .border-left { border-left: 1px solid #000; }
        .border-bottom { border-bottom: 1px solid #000; }
        .p-2 { padding: 2px; }
        .p-4 { padding: 4px; }
        .p-6 { padding: 6px; }
        .p-8 { padding: 8px; }

        /* Tabela de itens */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        table th {
            text-align: center;
        }

        /* Assinaturas */
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 40px;
            margin-bottom: 10px;
        }

        .signature-cell {
            display: table-cell;
            text-align: center;
            font-size: 10px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 0 30px 2px 30px;
        }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>

<body>
    <div class="container">

        <!-- TÍTULO PRINCIPAL -->
        <div class="title">
            REQUISIÇÃO DE COMPRA DE MATERIAIS
        </div>

        <!-- PRIMEIRA LINHA: LOGO + DADOS EMPRESA + BLOCO DIREITA -->
        <div class="table-row">

            <!-- LOGO (25%) -->
            <div class="table-cell logo-block" style="width:25%; border-right:1px solid #000;">
                <div style="font-size:10px; line-height:1.2;">
                    <strong>GRUPO<br>TERRA</strong><br>
                    <span>SOLUÇÕES COMPLETAS EM<br>CONSTRUÇÃO E AMBIENTES</span>
                </div>
            </div>

            <!-- DADOS EMPRESA (50%) -->
            <div class="table-cell" style="width:50%; border-right:1px solid #000; padding:6px 8px;">
                <div style="font-size:11px;">
                    <strong>GRUPO TERRA</strong><br>
                    Centro de Produção e Logística<br>
                    End: {{ $group->address ?? '' }}<br>
                    República de Angola<br>
                    NIF: {{ $group->nif ?? '—' }}<br>
                    Telefone: {{ $group->phone ?? '—' }}
                </div>
            </div>

            <!-- BLOCO DIREITA (25%) -->
            <div class="table-cell" style="width:25%;">
                <!-- CÓDIGO RCM -->
                <div style="border-bottom:1px solid #000; text-align:right;">
                    <div style="border-left:1px solid #000; padding:6px 8px; font-size:12px; font-weight:bold;">
                        RCM {{ $requisition->code }}/{{ now()->year }}
                    </div>
                </div>

                <!-- DATA / PRIORIDADE / REQUERENTE -->
                <div style="border-left:1px solid #000;">
                    <div style="border-bottom:1px solid #000; padding:4px 8px; font-size:11px; font-weight:bold; text-align:center;">
                        DATA: {{ $requisition->created_at->format('d-m-Y') }}
                    </div>
                    <div style="border-bottom:1px solid #000; padding:2px 8px; font-size:9px;">
                        NIVEL DE PRIORIDADE
                    </div>
                    <div style="border-bottom:1px solid #000; padding:2px 8px; font-size:9px;">
                        {{ strtoupper($requisition->priority ?? '-') }}
                    </div>
                    <div style="border-bottom:1px solid #000; padding:2px 8px; font-size:9px;">
                        REQUERENTE
                    </div>
                    <div style="padding:2px 8px; font-size:9px;">
                        {{ $requisition->requester_name ?? ($requisition->user->name ?? '-') }}
                    </div>
                </div>
            </div>

        </div>

        <!-- LINHAS CLIENTE / NIF / ALMOXERIFE / EMPRESA -->
        <div class="table-row" style="display:block;">
            <div style="border-bottom:1px solid #000; padding:2px 8px;">
                <strong>CLIENTE:</strong> {{ $client?->name ?? ($company->name ?? '-') }}
            </div>
            <div style="border-bottom:1px solid #000; padding:2px 8px;">
                <strong>NIF:</strong> {{ $group->nif ?? '—' }}
            </div>
            <div style="border-bottom:1px solid #000; padding:2px 8px;">
                <strong>ALMOXERIFE:</strong> {{ $requisition->user->name ?? '-' }}
            </div>
            <div style="border-bottom:1px solid #000; padding:2px 8px;">
                <strong>EMPRESA :</strong> {{ $group->name ?? 'TERRA INTERIOR' }} - {{ $company->name ?? 'INDUSTRIA' }}
            </div>
        </div>

        <!-- TABELA ITENS -->
        <div style="margin:0; padding:0;">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th style="width:5%;">Item</th>
                        <th style="width:45%;">DESCRIÇÃO DO MATERIAL</th>
                        <th style="width:15%;">DATA DE CHEGADA DO MATERIAL</th>
                        <th style="width:20%;">FINALIDADE</th>
                        <th style="width:7%;">UND.</th>
                        <th style="width:8%;">QNTDS.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requisition->items as $index => $it)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $it->product->name ?? '—' }}</td>
                            <td></td>
                            <td>{{ $requisition->purpose ?? '-' }}</td>
                            <td class="text-center">{{ $it->product->unit->symbol ?? '-' }}</td>
                            <td class="text-center">{{ number_format($it->requested_quantity ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="color:#888; padding:8px;">Nenhuma requisição encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ASSINATURAS EM LINHA -->
        <div class="signature-row">
            <div class="signature-cell">
                <div class="signature-line"></div>
                O REQUERENTE
            </div>
            <div class="signature-cell">
                <div class="signature-line"></div>
                GESTOR COMPRAS
            </div>
        </div>

    </div>
</body>
</html>
