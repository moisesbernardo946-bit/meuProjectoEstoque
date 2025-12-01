{{-- resources/views/almoxarife/products/index.blade.php --}}
@extends('layouts.almoxarife')

@section('title', 'Produtos')
@section('page-title', 'Produtos')
@section('page-subtitle', 'Catálogo de produtos do almoxarifado')

@section('page-actions')
    <a href="{{ route('almoxarife.products.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Novo Produto
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                        placeholder="Pesquisar por nome, código ou categoria">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('almoxarife.products.index') }}" class="btn btn-sm btn-outline-light border">
                        Limpar
                    </a>
                </div>
                <div class="col-md-5 text-end">
                    <span class="text-muted small">
                        Total de produtos: <strong>{{ $totalProducts }}</strong>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Medida</th>
                            <th>Unidade</th>
                            <th>Zona</th>
                            <th class="text-center">QR</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td><span class="badge text-bg-light border">{{ $product->code }}</span></td>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td>{{ $product->category?->name }}</td>
                                <td>{{ $product->measure }}</td>
                                <td>{{ $product->unit?->symbol ?? $product->unit?->name }}</td>
                                <td>{{ $product->zone?->name }}</td>
                                <td class="text-center">
                                    @if ($product->qr_code_path)
                                        <a href="{{ asset($product->qr_code_path) }}" target="_blank" download>
                                            <i class="bi bi-qr-code-scan"></i>
                                        @else
                                            <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('almoxarife.products.edit', $product->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('almoxarife.products.destroy', $product->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Nenhum produto encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-2">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
