@extends('layouts.almoxarife')

@section('title', 'Nova Zona')
@section('page-title', 'Nova Zona')
@section('page-subtitle', 'Cadastrar uma nova zona de produto')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.zones.store') }}" method="POST">
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
                    <label class="form-label">Localização</label>
                    <textarea name="location"
                              class="form-control @error('location') is-invalid @enderror"
                              rows="3">{{ old('location') }}</textarea>
                    @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('almoxarife.zones.index') }}" class="btn btn-light border">
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
