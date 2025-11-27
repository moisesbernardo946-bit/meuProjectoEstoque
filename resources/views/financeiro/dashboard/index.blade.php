{{-- resources/views/financeiro/dashboard/index.blade.php --}}
@extends('layouts.financeiro')

@section('title', 'Dashboard Financeiro')
@section('page-title', 'Dashboard Financeiro')
@section('page-subtitle', 'Visão geral das programações de compra')

@section('content')
    {{-- alertas (se quiser usar) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Linha de cards com totais --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Programações Pendentes</span>
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalPendentes }}</h4>
                    <a href="{{ route('financeiro.programs.index', ['status' => 'pendente']) }}"
                        class="small text-decoration-none">
                        Ver pendentes →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Programações Aprovadas</span>
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalAprovadas }}</h4>
                    <a href="{{ route('financeiro.programs.index', ['status' => 'aprovado']) }}"
                        class="small text-decoration-none">
                        Ver aprovadas →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Programações Concluídas</span>
                        <i class="bi bi-flag"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalConcluidas }}</h4>
                    <a href="{{ route('financeiro.programs.index', ['status' => 'concluido']) }}"
                        class="small text-decoration-none">
                        Ver concluídas →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">Total de Programações</span>
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $totalTodas }}</h4>
                    <a href="{{ route('financeiro.programs.index') }}" class="small text-decoration-none">
                        Ver todas →
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimas programações pendentes --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Últimas programações pendentes</h6>
            <a href="{{ route('financeiro.programs.index', ['status' => 'pendente']) }}"
                class="btn btn-sm btn-outline-primary">
                Ver todas pendentes
            </a>
        </div>
        <div class="card-body p-0">
            @if ($ultimasPendentes->isEmpty())
                <p class="text-muted text-center my-3 mb-3">
                    Não há programações pendentes no momento.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Requisição</th>
                                <th>Empresa</th>
                                <th>Comprador</th>
                                <th>Criado em</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ultimasPendentes as $program)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $program->code }}</span>
                                    </td>
                                    <td>
                                        {{ $program->requisition?->code ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $program->company?->code }} - {{ $program->company?->name }}
                                    </td>
                                    <td>
                                        {{ $program->buyer_name ?? $program->buyer?->name }}
                                    </td>
                                    <td>
                                        {{ $program->created_at?->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('financeiro.programs.show', $program->id) }}"
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
