@extends('layouts.almoxarife')

@section('title', 'Editar Unidade')
@section('page-title', 'Editar Unidade')
@section('page-subtitle', 'Atualizar dados da unidade')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.units.update', $unit) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $unit->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Símbolo</label>
                    <textarea name="symbol"
                              class="form-control @error('symbol') is-invalid @enderror"
                              rows="3">{{ old('symbol', $unit->symbol) }}</textarea>
                    @error('symbol')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('almoxarife.units.index') }}" class="btn btn-light border">
                        Voltar
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
