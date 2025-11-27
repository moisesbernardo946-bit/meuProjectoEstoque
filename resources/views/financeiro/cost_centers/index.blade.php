@extends('layouts.financeiro')

@section('title', 'Centros de Custo')
@section('page-title', 'Centros de Custo')
@section('page-subtitle', 'Visão geral dos centros de custo do grupo')

@section('page-actions')
    <a href="{{ route('financeiro.cost_centers.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Centro de Custo
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Tipo</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="empresa" {{ request('type') === 'empresa' ? 'selected' : '' }}>Empresa</option>
                        <option value="cliente" {{ request('type') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small mb-1">Grupo</label>
                    <select name="company_group_id" class="form-select form-select-sm">
                        <option value="">Todos os grupos</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}"
                                {{ (string) $group->id === request('company_group_id') ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 align-self-end">
                    <button class="btn btn-sm btn-outline-primary">Filtrar</button>
                    <a href="{{ route('financeiro.cost_centers.index') }}" class="btn btn-sm btn-outline-secondary">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Lista de Centros de Custo</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 8%">Código</th>
                            <th>Nome</th>
                            <th style="width: 12%">Tipo</th>
                            <th>Grupo</th>
                            <th>Empresa</th>
                            <th>Cliente</th>
                            <th style="width: 10%">Ativo?</th>
                            <th style="width: 8%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($costCenters as $cc)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $cc->code }}
                                </td>
                                <td>
                                    {{ $cc->name }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $cc->type === 'empresa' ? 'primary' : 'info' }}">
                                        {{ strtoupper($cc->type) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $cc->group?->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $cc->company?->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $cc->client?->name ?? '-' }}
                                </td>
                                <td>
                                    @if ($cc->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('financeiro.cost_centers.show', $cc->id) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    Nenhum centro de custo cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($costCenters->hasPages())
                <div class="p-2">
                    {{ $costCenters->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
