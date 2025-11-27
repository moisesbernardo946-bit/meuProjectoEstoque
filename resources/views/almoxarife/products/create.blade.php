@extends('layouts.almoxarife')

@section('title', 'Novo Produto')
@section('page-title', 'Novo Produto')
@section('page-subtitle', 'Cadastrar um novo produto no almoxarifado')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.products.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Zona <span class="text-danger">*</span></label>
                        <select name="zone_id"
                                class="form-select @error('zone_id') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}"
                                    {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('zone_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Unidade <span class="text-danger">*</span></label>
                        <select name="unit_id"
                                class="form-select @error('unit_id') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} @if($unit->symbol) ({{ $unit->symbol }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Medida <span class="text-danger">*</span></label>
                        <input type="text" name="measure"
                               class="form-control @error('measure') is-invalid @enderror"
                               value="{{ old('measure') }}" placeholder="Ex.: 25kg, 2m, 500ml" required>
                        @error('measure')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('almoxarife.products.index') }}" class="btn btn-light border">
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
