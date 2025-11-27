<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Entradas de Produtos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3, h4 { margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; }
        .header small { font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $company->name ?? 'Empresa' }}</h2>
        <small>Relatório de Entradas de Produtos</small><br>
        <small>Emitido em {{ now()->format('d/m/Y H:i') }}</small>
    </div>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Data</th>
            <th>Produto</th>
            <th>Entidade</th>
            <th>Tipo</th>
            <th>Fornecedor</th>
            <th class="text-right">Qtd</th>
        </tr>
        </thead>
        <tbody>
        @forelse($entries as $entry)
            <tr>
                <td>{{ $entry->id }}</td>
                <td>{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    {{ $entry->entityProduct->product->name ?? '-' }}<br>
                    <small>{{ $entry->entityProduct->product->code ?? '' }}</small>
                </td>
                <td>
                    @if($entry->entityProduct->entity_type === 'client')
                        Cliente:
                    @else
                        Empresa:
                    @endif
                    {{ $entry->entityProduct->entity?->name }}
                </td>
                <td>{{ $entry->type }}</td>
                <td>{{ $entry->supplier ?? '-' }}</td>
                <td class="text-right">{{ number_format($entry->quantity) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Nenhuma entrada encontrada.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
