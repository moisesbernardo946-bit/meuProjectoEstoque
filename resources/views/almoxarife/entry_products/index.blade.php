@extends('layouts.almoxarife')

@section('title', 'Entradas de Produtos')
@section('page-title', 'Entradas de Produtos')
@section('page-subtitle', 'Lançamentos de entrada no estoque')

@section('page-actions')
    <a href="{{ route('almoxarife.entry_products.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle"></i> Nova Entrada
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
            <strong>Ops!</strong> Verifique os erros abaixo.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" name="product" class="form-control form-control-sm"
                           placeholder="Produto (nome ou código)..."
                           value="{{ request('product') }}">
                </div>

                <div class="col-md-3">
                    <input type="text" name="type" class="form-control form-control-sm"
                           placeholder="Tipo de entrada..."
                           value="{{ request('type') }}">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_start" class="form-control form-control-sm"
                           value="{{ request('date_start') }}" placeholder="De">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_end" class="form-control form-control-sm"
                           value="{{ request('date_end') }}" placeholder="Até">
                </div>

                <div class="col-12 d-flex justify-content-between mt-2">
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('almoxarife.entry_products.index') }}"
                           class="btn btn-sm btn-outline-secondary">
                            Limpar
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('almoxarife.entry_products.export.pdf', request()->all()) }}"
                           class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-filetype-pdf"></i> PDF
                        </a>
                        <a href="{{ route('almoxarife.entry_products.export.excel', request()->all()) }}"
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Entidade</th>
                        <th>Tipo</th>
                        <th>Fornecedor</th>
                        <th>Observação</th>
                        <th class="text-end">Qtd</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td>{{ $entry->id }}</td>
                            <td>{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $entry->entityProduct->product->name ?? '-' }}</strong><br>
                                <small class="text-muted">
                                    {{ $entry->entityProduct->product->code ?? '' }}
                                </small>
                            </td>
                            <td>
                                @if($entry->entityProduct->entity_type === 'client')
                                    <span class="badge bg-success">Cliente</span><br>
                                @else
                                    <span class="badge bg-primary">Empresa</span><br>
                                @endif
                                <small>
                                    {{ $entry->entityProduct->entity?->code }} -
                                    {{ $entry->entityProduct->entity?->name }}
                                </small>
                            </td>
                            <td>{{ $entry->type }}</td>
                            <td>{{ $entry->supplier ?? '-' }}</td>
                            <td>{{ $entry->notes ?? '-' }}</td>
                            <td class="text-end">{{ number_format($entry->quantity) }}</td>
                            <td class="text-end">
                                <a href="{{ route('almoxarife.entry_products.show', $entry->id) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('almoxarife.entry_products.edit', $entry->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Nenhuma entrada encontrada.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($entries, 'links'))
                <div class="mt-2">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
