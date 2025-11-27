@extends('layouts.comprador')

@section('title', 'Programações de Compra')
@section('page-title', 'Programações de Compra')
@section('page-subtitle', 'Gestão das programações de compra enviadas ao financeiro')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Filtros --}}
            <form method="GET" action="{{ route('comprador.programs.index') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Código / Requisição / Solicitante</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                        placeholder="Buscar...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="aprovado" {{ request('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                        <option value="rejeitado" {{ request('status') == 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>

            @if ($programs->count() === 0)
                <p class="text-muted mb-0">Nenhuma programação de compra encontrada.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Requisição</th>
                                <th>Solicitante</th>
                                <th>Prioridade</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programs as $program)
                                @php
                                    $req = $program->requisition;
                                @endphp
                                <tr>
                                    <td>{{ $program->code }}</td>
                                    <td>
                                        @if ($req)
                                            {{ $req->code }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $req?->requester_name ?? '—' }}
                                    </td>
                                    <td>
                                        @if ($req)
                                            <span class="badge
                                                @switch($req->priority)
                                                    @case('baixa') bg-success-subtle text-success @break
                                                    @case('media') bg-info-subtle text-info @break
                                                    @case('alta') bg-warning-subtle text-warning @break
                                                    @case('urgente') bg-danger-subtle text-danger @break
                                                    @default bg-secondary
                                                @endswitch
                                            ">
                                                {{ ucfirst($req->priority) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge
                                            @switch($program->status)
                                                @case('pendente') bg-warning-subtle text-warning @break
                                                @case('aprovado') bg-success-subtle text-success @break
                                                @case('rejeitado') bg-danger-subtle text-danger @break
                                                @case('concluido') bg-primary-subtle text-primary @break
                                                @default bg-secondary
                                            @endswitch
                                        ">
                                            {{ ucfirst($program->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $program->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('comprador.programs.show', $program->id) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if ($program->status === 'pendente')
                                            <a href="{{ route('comprador.programs.edit', $program->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $programs->withQueryString()->links() }}
            @endif
        </div>
    </div>
@endsection
