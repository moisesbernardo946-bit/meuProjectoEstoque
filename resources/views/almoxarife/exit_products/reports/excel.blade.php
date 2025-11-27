<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Data</th>
            <th>Produto</th>
            <th>Entidade</th>
            <th>Tipo</th>
            <th>Quantidade</th>
            <th>Observações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($exits as $exit)
            @php
                $ep = $exit->entityProduct;
                $produto = $ep?->product;
                $entidade = $ep?->entity;
            @endphp
            <tr>
                <td>{{ $exit->id }}</td>
                <td>{{ $exit->created_at?->format('d/m/Y H:i') }}</td>
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
                <td>{{ $exit->type }}</td>
                <td>{{ $exit->quantity }}</td>
                <td>{{ $exit->notes ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
