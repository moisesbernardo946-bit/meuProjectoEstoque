@extends('layouts.almoxarife')

@section('title', 'Categorias')
@section('page-title', 'Categorias de Produtos')
@section('page-subtitle', 'Gerencie as categorias usadas no almoxarifado')

@section('page-actions')
    <a href="{{ route('almoxarife.categories.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nova Categoria
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ $search }}"
                           class="form-control form-control-sm"
                           placeholder="Pesquisar por nome ou descrição">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('almoxarife.categories.index') }}"
                       class="btn btn-sm btn-outline-light border">
                        Limpar
                    </a>
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
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td>{{ Str::limit($category->description, 60) }}</td>
                            <td class="text-end">
                                <a href="{{ route('almoxarife.categories.edit', $category) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('almoxarife.categories.destroy', $category) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
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
                            <td colspan="4" class="text-center text-muted py-4">
                                Nenhuma categoria encontrada.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-2">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection
