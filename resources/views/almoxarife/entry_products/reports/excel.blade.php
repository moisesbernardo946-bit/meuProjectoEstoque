<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Data</th>
            <th>Produto</th>
            <th>Entidade</th>
            <th>Tipo</th>
            <th>Fornecedor</th>
            <th>Quantidade</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entries as $entry)
            @php
                $ep = $entry->entityProduct;
                $produto = $ep?->product;
                $entidade = $ep?->entity;
            @endphp
            <tr>
                <td>{{ $entry->id }}</td>
                <td>{{ $entry->created_at?->format('d/m/Y H:i') }}</td>
                <td>{{ $produto?->name ?? '-' }}</td>
                <td>
                    @if($ep?->entity_type === 'client')
                        Cliente: {{ $entidade?->name ?? '-' }}
                    @elseif($ep?->entity_type === 'company')
                        Empresa: {{ $entidade?->name ?? '-' }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $entry->type }}</td>
                <td>{{ $entry->supplier ?? '-' }}</td>
                <td>{{ $entry->quantity }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
