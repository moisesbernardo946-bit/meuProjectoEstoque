@extends('layouts.motorista')

@section('title', 'Dashboard do Motorista')
@section('page-title', 'Dashboard do Motorista')
@section('page-subtitle', 'Visão das programações aprovadas para entrega')

@section('content')
    <div class="row g-3 mb-3">
        {{-- Programações aprovadas pendentes de conclusão --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Programações aprovadas</div>
                        <h4 class="fw-bold mb-0">{{ $approvedCount }}</h4>
                        <small class="text-muted">Aguardando conclusão / movimentação</small>
                    </div>
                    <div class="display-6 text-primary">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                </div>
                <div class="card-footer bg-light py-2">
                    <a href="{{ route('motorista.motoristaPrograms.index') }}" class="small text-decoration-none">
                        Ver todas as programações aprovadas →
                    </a>
                </div>
            </div>
        </div>

        {{-- Programações parciais --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Programações parciais</div>
                        <h4 class="fw-bold mb-0">{{ $partialCount }}</h4>
                        <small class="text-muted">Com itens faltando</small>
                    </div>
                    <div class="display-6 text-warning">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                </div>
                <div class="card-footer bg-light py-2">
                    <a href="{{ route('motorista.motoristaPrograms.index', ['status' => 'parcial']) }}"
                        class="small text-decoration-none">
                        Ver programações parciais →
                    </a>
                </div>
            </div>
        </div>

        {{-- Programações concluídas --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Programações concluídas</div>
                        <h4 class="fw-bold mb-0">{{ $completedCount }}</h4>
                        <small class="text-muted">Finalizadas pelo motorista</small>
                    </div>
                    <div class="display-6 text-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div class="card-footer bg-light py-2">
                    <a href="{{ route('motorista.motoristaPrograms.index', ['status' => 'concluido']) }}"
                        class="small text-decoration-none">
                        Ver programações concluídas →
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista simples das últimas programações aprovadas --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Últimas programações aprovadas</strong>
            <a href="{{ route('motorista.motoristaPrograms.index') }}" class="small text-decoration-none">
                Ver todas
            </a>
        </div>
        <div class="card-body p-0">
            @if ($latestPrograms->isEmpty())
                <p class="text-muted m-3 mb-0">
                    Nenhuma programação aprovada encontrada.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Requisição</th>
                                <th>Empresa</th>
                                <th>Criada em</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 120px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestPrograms as $program)
                                <tr>
                                    <td><strong>{{ $program->code }}</strong></td>
                                    <td>{{ $program->requisition?->code ?? '—' }}</td>
                                    <td>{{ $program->company?->name ?? '—' }}</td>
                                    <td>{{ $program->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $cls = 'secondary';
                                            $label = $program->status;

                                            switch ($program->status) {
                                                case 'aprovado':
                                                    $cls = 'primary';
                                                    $label = 'Aprovado';
                                                    break;
                                                case 'parcial':
                                                    $cls = 'warning';
                                                    $label = 'Parcial';
                                                    break;
                                                case 'concluido':
                                                    $cls = 'success';
                                                    $label = 'Concluído';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $cls }}">{{ $label }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('motorista.motoristaPrograms.show', $program->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
