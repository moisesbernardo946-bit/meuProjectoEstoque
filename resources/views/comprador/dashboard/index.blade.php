@extends('layouts.comprador')

@section('title', 'Dashboard do Comprador')
@section('page-title', 'Dashboard do Comprador')
@section('page-subtitle', 'Visão geral das programações de compra')

@section('content')

    {{-- Alertas de sessão --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- RESUMO RÁPIDO --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Programações Pendentes</span>
                        <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-0">
                        {{ $stats['pendente'] ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Programações Aprovadas</span>
                        <i class="bi bi-check2-circle text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-0">
                        {{ $stats['aprovado'] ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Programações Concluídas</span>
                        <i class="bi bi-flag-fill text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-0">
                        {{ $stats['concluido'] ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Total Programações</span>
                        <i class="bi bi-list-check text-secondary"></i>
                    </div>
                    <h4 class="fw-bold mb-0">
                        {{ $stats['total'] ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS E AÇÕES RÁPIDAS --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('comprador.dashboard') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="aprovado" {{ request('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small mb-1">Pesquisar</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Código da programação, requisição ou solicitante...">
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Período (data criação)</label>
                    <div class="input-group input-group-sm">
                        <input type="date"
                               name="date_from"
                               value="{{ request('date_from') }}"
                               class="form-control form-control-sm">
                        <span class="input-group-text">até</span>
                        <input type="date"
                               name="date_to"
                               value="{{ request('date_to') }}"
                               class="form-control form-control-sm">
                    </div>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-primary flex-fill">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('comprador.dashboard') }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- LISTA DE PROGRAMAÇÕES RECENTES --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <strong>Programações de compra recentes</strong>
                <small class="text-muted d-block">
                    Listagem das últimas programações criadas por você na empresa
                    {{ $company->code ?? '' }} - {{ $company->name ?? '' }}
                </small>
            </div>
            <div>
                <a href="{{ route('comprador.requisitions.index') }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-file-earmark-plus"></i>
                    Nova programação (a partir de requisição)
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($programs->count() === 0)
                <p class="text-muted p-3 mb-0">
                    Nenhuma programação encontrada com os filtros aplicados.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Requisição</th>
                                <th>Status</th>
                                <th>Comprador</th>
                                <th>Qtd Itens</th>
                                <th>Criado em</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programs as $program)
                                <tr>
                                    <td>
                                        <a href="{{ route('comprador.programs.show', $program->id) }}">
                                            <span class="fw-semibold">{{ $program->code }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        @if ($program->requisition)
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                {{ $program->requisition->code }}
                                            </span>
                                            <div class="small text-muted">
                                                {{ $program->requisition->requester_name }}
                                                — prioridade: {{ ucfirst($program->requisition->priority) }}
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $program->status;
                                            $badgeClass = match ($status) {
                                                'pendente' => 'bg-warning-subtle text-warning border-warning',
                                                'aprovado' => 'bg-success-subtle text-success border-success',
                                                'concluido' => 'bg-primary-subtle text-primary border-primary',
                                                default => 'bg-secondary-subtle text-secondary border-secondary',
                                            };
                                        @endphp
                                        <span class="badge border {{ $badgeClass }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">
                                            {{ $program->buyer_name ?? $program->buyer?->name }}
                                        </div>
                                        @if ($program->buyer_email)
                                            <div class="small text-muted">
                                                {{ $program->buyer_email }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $program->items_count ?? $program->items->count() }}
                                    </td>
                                    <td>
                                        <span class="small">
                                            {{ $program->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('comprador.programs.show', $program->id) }}"
                                               class="btn btn-outline-secondary">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            {{-- Só pode editar se não estiver aprovado ou concluído --}}
                                            @if (!in_array($program->status, ['aprovado', 'concluido']))
                                                <a href="{{ route('comprador.programs.edit', $program->id) }}"
                                                   class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                <div class="p-2">
                    {{ $programs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
