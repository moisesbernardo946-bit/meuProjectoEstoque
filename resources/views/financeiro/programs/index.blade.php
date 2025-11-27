@extends('layouts.financeiro')

@section('title', 'Programações de Compra')
@section('page-title', 'Programações de Compra')
@section('page-subtitle', 'Programações aprovadas (visão do financeiro)')

@section('content')
    {{-- FILTROS --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('financeiro.programs.index') }}" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Pesquisar</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Código da programação, código da requisição, comprador..."
                           value="{{ $search }}">
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABELA --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Programações de compra aprovadas</strong>
            <span class="text-muted small">{{ $programs->total() }} registro(s)</span>
        </div>
        <div class="card-body p-0">
            @if ($programs->count() === 0)
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
                                <th>Comprador</th>
                                <th>Aprovada em</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 140px;">Total Orçado</th>
                                <th class="text-end" style="width: 140px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programs as $program)
                                <tr>
                                    <td>
                                        <strong>{{ $program->code }}</strong>
                                    </td>
                                    <td>
                                        {{ $program->requisition?->code ?? '—' }}
                                    </td>
                                    <td>
                                        {{ $program->buyer_name ?? ($program->buyer?->name ?? '—') }}<br>
                                        <small class="text-muted">
                                            {{ $program->buyer_email ?? $program->buyer?->email }}
                                        </small>
                                    </td>
                                    <td>
                                        {{-- se tiver campo approved_at, melhor usar ele; senão, created_at --}}
                                        {{ $program->updated_at?->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Aprovado (Diretor)</span>
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($program->total_budget_value ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('financeiro.programs.show', $program->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver detalhes
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-2">
                    {{ $programs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
