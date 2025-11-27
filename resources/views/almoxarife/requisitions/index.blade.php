@extends('layouts.almoxarife')

@section('title', 'Requisições')
@section('page-title', 'Requisições')
@section('page-subtitle', 'Gestão de requisições de materiais')

@section('page-actions')
    <a href="{{ route('almoxarife.requisitions.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle"></i> Nova Requisição
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Código, solicitante, finalidade..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Status --</option>
                        <option value="pendente" {{ $status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="aprovada" {{ $status === 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                        <option value="rejeitada" {{ $status === 'rejeitada' ? 'selected' : '' }}>Rejeitada</option>
                        <option value="concluida" {{ $status === 'concluida' ? 'selected' : '' }}>Concluída</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">-- Prioridade --</option>
                        <option value="baixa" {{ $priority === 'baixa' ? 'selected' : '' }}>Baixa</option>
                        <option value="media" {{ $priority === 'media' ? 'selected' : '' }}>Média</option>
                        <option value="alta" {{ $priority === 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="urgente" {{ $priority === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}"
                        placeholder="De">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}"
                        placeholder="Até">
                </div>

                <div class="col-12 d-flex justify-content-between mt-2">
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('almoxarife.requisitions.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Solicitante</th>
                            <th>Cliente</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th class="text-end">Qtd itens</th>
                            <th class="text-end">Qtd solicitada</th>
                            <th class="text-end">Qtd entregue</th>
                            <th class="text-end">Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requisitions as $req)
                            @php
                                $itensCount = $req->items->count();
                                $totalReq = $req->items->sum('requested_quantity');
                                $totalEntregue = $req->items->sum('delivered_quantity');
                                $statusClass = match ($req->status) {
                                    'aprovado' => 'success',
                                    'rejeitado' => 'danger',
                                    'pendente' => 'warning',
                                    'concluido' => 'primary',
                                    default => 'secondary',
                                };
                                $priorityClass = match ($req->priority) {
                                    'baixa' => 'secondary',
                                    'media' => 'info',
                                    'alta' => 'warning',
                                    'urgente' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $req->id }}</td>
                                <td>{{ $req->code }}</td>
                                <td>{{ $req->requester_name }}</td>
                                <td>{{ $req->client?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $priorityClass }}">
                                        {{ ucfirst($req->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="text-end">{{ $itensCount }}</td>
                                <td class="text-end">{{ $totalReq }}</td>
                                <td class="text-end">{{ $totalEntregue }}</td>
                                <td class="text-end small text-muted">
                                    {{ $req->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-end">
                                    {{-- Sempre botão show --}}
                                    <a href="{{ route('almoxarife.requisitions.show', $req->id) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if ($req->status === 'pendente')
                                        {{-- Pode editar só se pendente --}}
                                        <a href="{{ route('almoxarife.requisitions.edit', $req->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @elseif(in_array($req->status, ['aprovado', 'parcial', 'em curso']))
                                        {{-- Botão para fazer entrada de produtos (delivered_quantity) --}}
                                        <a href="{{ route('almoxarife.requisitions.receive', $req->id) }}"
                                            class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-box-arrow-in-down"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-3">
                                    Nenhuma requisição encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2 px-3 pb-2">
                {{ $requisitions->links() }}
            </div>
        </div>
    </div>
@endsection
