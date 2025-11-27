{{-- resources/views/financeiro/financial_movements/index.blade.php --}}
@extends('layouts.financeiro')

@section('title', 'Movimentos Financeiros')
@section('page-title', 'Movimentos Financeiros')
@section('page-subtitle', 'Lançamentos por centro de custo')

@section('page-actions')
    <a href="{{ route('financeiro.financial_movements.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Movimento
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
                <div class="col-md-4">
                    <label class="form-label small mb-1">Centro de Custo</label>
                    <select name="cost_center_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach ($costCenters as $cc)
                            <option value="{{ $cc->id }}" @selected(request('cost_center_id') == $cc->id)>
                                {{ $cc->code }} - {{ $cc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">Tipo</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="receita" @selected(request('type') == 'receita')>Receita</option>
                        <option value="custo" @selected(request('type') == 'custo')>Custo</option>
                        <option value="despesa" @selected(request('type') == 'despesa')>Despesa</option>
                    </select>
                </div>

                <div class="col-md-3 align-self-end">
                    <button class="btn btn-sm btn-outline-primary">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>Lista de Movimentos</strong>
            <span class="small text-muted">
                Total: {{ $movements->total() }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Centro de Custo</th>
                            <th>Tipo</th>
                            <th class="text-end">Valor</th>
                            <th>Referência</th>
                            <th>Descrição</th>
                            <th style="width: 80px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $mv)
                            <tr>
                                <td>{{ $mv->movement_date->format('d/m/Y') }}</td>
                                <td>
                                    {{ $mv->costCenter?->code }} - {{ $mv->costCenter?->name }}
                                </td>
                                <td>
                                    @if ($mv->type === 'receita')
                                        <span class="badge bg-success">RECEITA</span>
                                    @elseif($mv->type === 'custo')
                                        <span class="badge bg-warning text-dark">CUSTO</span>
                                    @else
                                        <span class="badge bg-danger">DESPESA</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{ number_format($mv->amount, 2, ',', '.') }}
                                </td>
                                <td>{{ $mv->reference }}</td>
                                <td>{{ $mv->description }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('financeiro.financial_movements.edit', $mv->id) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('financeiro.financial_movements.destroy', $mv->id) }}"
                                            onsubmit="return confirm('Remover este movimento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Nenhum movimento financeiro encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-2">
                {{ $movements->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
