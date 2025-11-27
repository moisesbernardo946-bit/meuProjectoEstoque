<div class="table-responsive" style="max-height: 65vh; overflow-y: auto; overflow-x: auto;">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-primary sticky-top">
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Medida</th>
                <th>Categoria</th>
                <th>Unidade</th>
                <th>Zona</th>
                <th>Descrição</th>
                <th>Código</th>
                <th>QR</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->measure }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->unit->symbol ?? '-' }}</td>
                    <td>{{ $p->zone->name ?? '-' }}</td>
                    <td>{{ $p->description ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark">{{ $p->code }}</span></td>
                    <td>
                        @if ($p->qr_code_path)
                            <a href="{{ asset('storage/'.$p->qr_code_path) }}" download>
                                <img src="{{ asset('storage/'.$p->qr_code_path) }}" alt="QR" width="40" class="rounded shadow-sm">
                            </a>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('almoxarife.products.edit', $p->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('almoxarife.products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted py-3">Nenhum produto encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 px-3">
    {{ $products->links() }}
</div>
