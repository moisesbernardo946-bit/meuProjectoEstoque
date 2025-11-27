@extends('layouts.almoxarife')

@section('title', 'Produtos por Entidade')
@section('page-title', 'Produtos por Entidade')
@section('page-subtitle', 'Associações de produtos para clientes/empresa')

@section('page-actions')
    <a href="{{ route('almoxarife.entity_products.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle"></i> Nova Associação
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Buscar produto</label>
                    <input type="text" name="search" class="form-control" value="{{ $search }}"
                        placeholder="Nome ou código">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tipo de entidade</label>
                    <select name="entity_type" class="form-select">
                        <option value="">Todos</option>
                        <option value="company" {{ $entityType === 'company' ? 'selected' : '' }}>Empresa</option>
                        <option value="client" {{ $entityType === 'client' ? 'selected' : '' }}>Cliente</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cliente (da empresa {{ $company->code }})</label>
                    <select name="entity_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($clients as $c)
                            <option value="{{ $c->id }}" {{ $entityId == $c->id ? 'selected' : '' }}>
                                {{ $c->code }} - {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Para filtrar por empresa, escolha "Empresa" no tipo e deixe aqui vazio.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="critico" {{ request('status') == 'critico' ? 'selected' : '' }}>Crítico (empresa)
                        </option>
                        <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>Normal (empresa)
                        </option>
                        <option value="excesso" {{ request('status') == 'excesso' ? 'selected' : '' }}>Excesso (emp/cli)
                        </option>
                        <option value="vazio" {{ request('status') == 'vazio' ? 'selected' : '' }}>Vazio (cliente)
                        </option>
                        <option value="faltando" {{ request('status') == 'faltando' ? 'selected' : '' }}>Faltando (cliente)
                        </option>
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído
                            (cliente)</option>
                    </select>
                </div>

                <div class="col-md-2 align-self-end">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>

                <div class="col-md-2 align-self-end">
                    <a href="{{ route('almoxarife.entity_products.index') }}" class="btn btn-outline-dark w-100">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Associações encontradas ({{ $entityProducts->total() }})</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Produto</th>
                        <th>Entidade</th>
                        <th>Tipo</th>
                        <th class="text-end">Qtd</th>
                        <th class="text-end">Req. Cliente</th>
                        <th class="text-end">Min</th>
                        <th class="text-end">Max</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entityProducts as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <strong>{{ $item->product?->name }}</strong><br>
                                <small class="text-muted">{{ $item->product?->code }}</small>
                            </td>
                            <td>
                                @if ($item->entity_type === 'company')
                                    <span class="badge bg-primary">Empresa</span><br>
                                    <small>{{ $item->company?->code }} - {{ $item->company?->name }}</small>
                                @else
                                    <span class="badge bg-success">Cliente</span><br>
                                    <small>{{ $item->entity?->code }} - {{ $item->entity?->name }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $item->entity_type === 'company' ? 'Empresa' : 'Cliente' }}
                            </td>
                            <td class="text-end">
                                {{ number_format($item->quantity ?? 0) }}
                            </td>
                            <td class="text-end">
                                {{ $item->requested_quantity ? number_format($item->requested_quantity) : '-' }}
                            </td>
                            <td class="text-end">
                                {{ $item->min_stock ? number_format($item->min_stock) : '-' }}
                            </td>
                            <td class="text-end">
                                {{ $item->max_stock ? number_format($item->max_stock) : '-' }}
                            </td>
                            <td>
                                <span
                                    class="badge
        @if ($item->computed_status === 'critico') bg-danger
        @elseif($item->computed_status === 'normal') bg-success
        @elseif($item->computed_status === 'excesso') bg-warning text-dark
        @elseif($item->computed_status === 'vazio') bg-secondary
        @elseif($item->computed_status === 'faltando') bg-info text-dark
        @elseif($item->computed_status === 'concluido') bg-success
        @else bg-light text-muted @endif
    ">
                                    {{ ucfirst($item->computed_status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('almoxarife.entity_products.show', $item->id) }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('almoxarife.entity_products.edit', $item->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('almoxarife.entity_products.destroy', $item->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Tem certeza que deseja remover esta associação?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-3">
                                Nenhuma associação encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($entityProducts->hasPages())
            <div class="card-footer">
                {{ $entityProducts->links() }}
            </div>
        @endif
    </div>
@endsection
