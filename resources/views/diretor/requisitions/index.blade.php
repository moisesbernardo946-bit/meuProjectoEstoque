@extends('layouts.diretor')

@section('title', 'Requisições')
@section('page-title', 'Requisições')
@section('page-subtitle', 'Análise e aprovação de requisições')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-sm">
            {{ session('warning') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- Filtros --}}
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        @foreach ($availableStatuses as $key => $label)
                            <option value="{{ $key }}" {{ $statusFilter === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label mb-1">Pesquisar</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Código, Requisitante..."
                           value="{{ $search }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary me-2">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('diretor.requisitions.index') }}" class="btn btn-sm btn-outline-secondary">
                        Limpar
                    </a>
                </div>
            </form>

            {{-- Tabela --}}
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Data</th>
                            <th>Cliente / Destinatário</th>
                            <th>Requisitante</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requisitions as $req)
                            <tr>
                                <td>{{ $req->code }}</td>
                                <td>{{ $req->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($req->client)
                                        {{ $req->client->code }} - {{ $req->client->name }}
                                    @else
                                        {{ $company->code ?? 'EMP' }} - {{ $company->name }} (PRÓPRIA EMPRESA)
                                    @endif
                                </td>
                                <td>{{ $req->requester_name }}</td>
                                <td>
                                    @php
                                        $priorityLabels = [
                                            'baixa' => 'Baixa',
                                            'media' => 'Média',
                                            'alta' => 'Alta',
                                            'urgente' => 'Urgente',
                                        ];
                                    @endphp
                                    <span class="badge
                                        @switch($req->priority)
                                            @case('baixa') bg-secondary @break
                                            @case('media') bg-info @break
                                            @case('alta') bg-warning text-dark @break
                                            @case('urgente') bg-danger @break
                                            @default bg-secondary
                                        @endswitch
                                    ">
                                        {{ $priorityLabels[$req->priority] ?? ucfirst($req->priority) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $status = $req->status;
                                    @endphp
                                    <span class="badge
                                        @switch($status)
                                            @case('pendente') bg-secondary @break
                                            @case('aprovado') bg-success @break
                                            @case('parcial') bg-warning text-dark @break
                                            @case('rejeitado') bg-danger @break
                                            @case('em curso') bg-info @break
                                            @case('concluido') bg-primary @break
                                            @default bg-secondary
                                        @endswitch
                                    ">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('diretor.requisitions.show', $req->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if ($req->status === 'pendente')
                                        <a href="{{ route('diretor.requisitions.approval.form', $req->id) }}"
                                           class="btn btn-sm btn-success mt-1">
                                            <i class="bi bi-check2-square"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Nenhuma requisição encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            <div class="mt-2">
                {{ $requisitions->withQueryString()->links() }}
            </div>
        </div>
    </div>

@endsection
