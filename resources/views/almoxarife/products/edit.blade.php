@extends('layouts.almoxarife')

@section('title', 'Editar Produto')
@section('page-title', 'Editar Produto')
@section('page-subtitle', 'Atualizar informações do produto')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        <select name="zone_id" class="form-select @error('zone_id') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}"
                                    {{ old('zone_id', $product->zone_id) == $zone->id ? 'selected' : '' }}>
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
                        <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                            <option value="">-- Selecione --</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} @if ($unit->symbol)
                                        ({{ $unit->symbol }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Medida <span class="text-danger">*</span></label>
                        <input type="text" name="measure" class="form-control @error('measure') is-invalid @enderror"
                            value="{{ old('measure', $product->measure) }}" required>
                        @error('measure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if ($product->qr_code_path && file_exists(public_path($product->qr_code_path)))
                        <div class="col-12">
                            <label class="form-label">QR Code atual</label>
                            <div>
                                <img src="{{ asset($product->qr_code_path) }}" alt="QR Code {{ $product->code }}"
                                    style="max-width: 150px;">
                            </div>
                            <small class="text-muted">Ao salvar, o QR Code será atualizado com as novas informações.</small>
                        </div>
                    @else
                        <div class="col-12">
                            <p class="text-muted small">QR Code ainda não gerado.</p>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('almoxarife.products.index') }}" class="btn btn-light border">
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
