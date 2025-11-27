{{-- resources/views/comprador/requisitions/index.blade.php --}}
@extends('layouts.comprador')

@section('title', 'Requisições para Programação')
@section('page-title', 'Requisições para Programação de Compra')
@section('page-subtitle', 'Selecione uma requisição aprovada ou parcial para criar uma programação')

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

    {{-- Filtros / Busca --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        <option value="aprovado" {{ $statusFilter == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                        <option value="parcial" {{ $statusFilter == 'parcial' ? 'selected' : '' }}>Parcial</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Busca</label>
                    <input type="text" name="search"
                           class="form-control form-control-sm"
                           placeholder="Código, requisitante..."
                           value="{{ $search }}">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('comprador.requisitions.index') }}" class="btn btn-sm btn-outline-secondary">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela de requisições --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Requisições aprovadas ou parciais</strong>
            <span class="text-muted small">
                Empresa: {{ $company->code }} - {{ $company->name }}
            </span>
        </div>

        <div class="card-body p-0">
            @if ($requisitions->count() === 0)
                <p class="p-3 text-muted mb-0">
                    Nenhuma requisição encontrada com os filtros selecionados.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cód. Req.</th>
                                <th>Data</th>
                                <th>Prioridade</th>
                                <th>Solicitante</th>
                                <th>Cliente / Destinatário</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requisitions as $req)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $req->code }}</span>
                                    </td>
                                    <td>{{ $req->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $priorityLabels = [
                                                'baixa' => 'Baixa',
                                                'media' => 'Média',
                                                'alta' => 'Alta',
                                                'urgente' => 'Urgente',
                                            ];
                                            $priorityColors = [
                                                'baixa' => 'secondary',
                                                'media' => 'info',
                                                'alta' => 'warning',
                                                'urgente' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $priorityColors[$req->priority] ?? 'secondary' }}">
                                            {{ $priorityLabels[$req->priority] ?? ucfirst($req->priority) }}
                                        </span>
                                    </td>
                                    <td>{{ $req->requester_name }}</td>
                                    <td>
                                        @if ($req->client)
                                            {{ $req->client->code }} - {{ $req->client->name }}
                                        @else
                                            <span class="text-muted">Própria empresa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                'pendente' => ['label' => 'Pendente', 'class' => 'secondary'],
                                                'aprovado' => ['label' => 'Aprovado', 'class' => 'success'],
                                                'parcial'  => ['label' => 'Parcial', 'class' => 'warning'],
                                                'rejeitado'=> ['label' => 'Rejeitado', 'class' => 'danger'],
                                                'em curso' => ['label' => 'Em curso', 'class' => 'info'],
                                                'concluido'=> ['label' => 'Concluído', 'class' => 'primary'],
                                            ];
                                            $st = $statusLabels[$req->status] ?? ['label' => $req->status, 'class' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $st['class'] }}">
                                            {{ $st['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{-- Criar programação com base nesta requisição --}}
                                        <a href="{{ route('comprador.programs.create', ['requisition_id' => $req->id]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-diagram-3"></i>
                                            Programar compra
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-2">
                    {{ $requisitions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
