{{-- resources/views/financeiro/financial_movements/edit.blade.php --}}
@extends('layouts.financeiro')

@section('title', 'Editar Movimento Financeiro')
@section('page-title', 'Editar Movimento Financeiro')
@section('page-subtitle', 'Atualize os dados do movimento selecionado')

@section('page-actions')
    <a href="{{ route('financeiro.financial_movements.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar para lista
    </a>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('financeiro.financial_movements.update', $movement->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Centro de Custo</label>
                        <select name="cost_center_id"
                                class="form-select form-select-sm @error('cost_center_id') is-invalid @enderror">
                            <option value="">-- Selecione --</option>
                            @foreach ($costCenters as $cc)
                                <option value="{{ $cc->id }}"
                                    {{ old('cost_center_id', $movement->cost_center_id) == $cc->id ? 'selected' : '' }}>
                                    {{ $cc->code }} - {{ $cc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cost_center_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipo de Movimento</label>
                        <select name="type"
                                class="form-select form-select-sm @error('type') is-invalid @enderror">
                            <option value="">-- Selecione --</option>
                            <option value="receita" {{ old('type', $movement->type) == 'receita' ? 'selected' : '' }}>
                                Receita
                            </option>
                            <option value="custo" {{ old('type', $movement->type) == 'custo' ? 'selected' : '' }}>
                                Custo
                            </option>
                            <option value="despesa" {{ old('type', $movement->type) == 'despesa' ? 'selected' : '' }}>
                                Despesa
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data</label>
                        <input type="date"
                               name="movement_date"
                               class="form-control form-control-sm @error('movement_date') is-invalid @enderror"
                               value="{{ old('movement_date', optional($movement->movement_date)->format('Y-m-d')) }}">
                        @error('movement_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Valor</label>
                        <input type="number" step="0.01" min="0"
                               name="amount"
                               class="form-control form-control-sm text-end @error('amount') is-invalid @enderror"
                               value="{{ old('amount', $movement->amount) }}">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição / Observações</label>
                    <textarea name="description" rows="3"
                              class="form-control form-control-sm @error('description') is-invalid @enderror">{{ old('description', $movement->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('financeiro.financial_movements.index') }}" class="btn btn-sm btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save"></i> Guardar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
