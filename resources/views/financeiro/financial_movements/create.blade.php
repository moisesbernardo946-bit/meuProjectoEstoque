{{-- resources/views/financeiro/financial_movements/create.blade.php --}}
@extends('layouts.financeiro')

@section('title', 'Novo Movimento Financeiro')
@section('page-title', 'Novo Movimento Financeiro')
@section('page-subtitle', 'Lançar receita / custo / despesa em um centro de custo')

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
        <div class="card-header bg-light">
            <strong>Dados do Lançamento</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('financeiro.financial_movements.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    {{-- Centro de Custo --}}
                    <div class="col-md-6">
                        <label class="form-label">Centro de Custo</label>
                        <select name="cost_center_id"
                            class="form-select form-select-sm @error('cost_center_id') is-invalid @enderror">
                            <option value="">-- Selecione --</option>
                            @foreach ($costCenters as $cc)
                                <option value="{{ $cc->id }}" @selected(old('cost_center_id') == $cc->id)>
                                    {{ $cc->code }} - {{ $cc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cost_center_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tipo --}}
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select form-select-sm @error('type') is-invalid @enderror">
                            <option value="">-- Selecione --</option>
                            <option value="receita" @selected(old('type') === 'receita')>Receita</option>
                            <option value="custo" @selected(old('type') === 'custo')>Custo</option>
                            <option value="despesa" @selected(old('type') === 'despesa')>Despesa</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Data --}}
                    <div class="col-md-3">
                        <label class="form-label">Data do Movimento</label>
                        <input type="date" name="movement_date"
                            class="form-control form-control-sm @error('movement_date') is-invalid @enderror"
                            value="{{ old('movement_date', now()->toDateString()) }}">
                        @error('movement_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    {{-- Valor --}}
                    <div class="col-md-3">
                        <label class="form-label">Valor</label>
                        <input type="number" name="amount" step="0.01" min="0"
                            class="form-control form-control-sm @error('amount') is-invalid @enderror"
                            value="{{ old('amount') }}">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Referência / Documento --}}
                    <div class="col-md-3">
                        <label class="form-label">Referência (opcional)</label>
                        <input type="text" name="reference"
                            class="form-control form-control-sm @error('reference') is-invalid @enderror"
                            value="{{ old('reference') }}"
                            placeholder="Ex.: PPG-000123, FAT-2025-001">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Descrição --}}
                <div class="mb-3">
                    <label class="form-label">Descrição (opcional)</label>
                    <input type="text" name="description"
                        class="form-control form-control-sm @error('description') is-invalid @enderror"
                        value="{{ old('description') }}"
                        placeholder="Ex.: Compra de insumos para projeto X, Receita do contrato Y, etc.">
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('financeiro.financial_movements.index') }}" class="btn btn-sm btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-circle"></i> Lançar Movimento
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
