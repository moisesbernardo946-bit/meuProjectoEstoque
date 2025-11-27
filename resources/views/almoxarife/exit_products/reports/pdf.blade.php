<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Saídas de Produtos</title>
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
        <small>Relatório de Saídas de Produtos</small><br>
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
            <th class="text-right">Qtd</th>
        </tr>
        </thead>
        <tbody>
        @forelse($exits as $exit)
            <tr>
                <td>{{ $exit->id }}</td>
                <td>{{ $exit->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    {{ $exit->entityProduct->product->name ?? '-' }}<br>
                    <small>{{ $exit->entityProduct->product->code ?? '' }}</small>
                </td>
                <td>
                    @if($exit->entityProduct->entity_type === 'client')
                        Cliente:
                    @else
                        Empresa:
                    @endif
                    {{ $exit->entityProduct->entity?->name }}
                </td>
                <td>{{ $exit->type }}</td>
                <td class="text-right">{{ number_format($exit->quantity) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Nenhuma saída encontrada.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
