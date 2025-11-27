@extends('layouts.almoxarife')

@section('title', 'Nova Unidade')
@section('page-title', 'Nova Unidade')
@section('page-subtitle', 'Cadastrar uma nova unidade de produto')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.units.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Símbolo</label>
                    <textarea name="symbol"
                              class="form-control @error('symbol') is-invalid @enderror"
                              rows="3">{{ old('symbol') }}</textarea>
                    @error('symbol')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('almoxarife.units.index') }}" class="btn btn-light border">
                        Voltar
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
