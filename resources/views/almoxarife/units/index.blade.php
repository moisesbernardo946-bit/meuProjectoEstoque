@extends('layouts.almoxarife')

@section('title', 'Unidades')
@section('page-title', 'Unidades de Produtos')
@section('page-subtitle', 'Gerencie as unidades usadas no almoxarifado')

@section('page-actions')
    <a href="{{ route('almoxarife.units.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nova Unidade
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
                           placeholder="Pesquisar por nome ou símbolo">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('almoxarife.units.index') }}"
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
                        <th>Símbolo</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <td>{{ $unit->id }}</td>
                            <td class="fw-semibold">{{ $unit->name }}</td>
                            <td>{{ Str::limit($unit->symbol) }}</td>
                            <td class="text-end">
                                <a href="{{ route('almoxarife.units.edit', $unit) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('almoxarife.units.destroy', $unit) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta unidade?')">
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
                                Nenhuma unidade encontrada.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-2">
                {{ $units->links() }}
            </div>
        </div>
    </div>
@endsection
