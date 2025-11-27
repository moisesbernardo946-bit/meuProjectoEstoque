@extends('layouts.almoxarife')

@section('title', 'Nova Entrada')
@section('page-title', 'Nova Entrada de Produto')
@section('page-subtitle', 'Registrar entrada no estoque')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ops!</strong> Verifique os erros abaixo.
                </div>
            @endif

            <form action="{{ route('almoxarife.entry_products.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="entity_product_id" class="form-label">Produto / Entidade</label>
                    <select name="entity_product_id" id="entity_product_id"
                            class="form-select @error('entity_product_id') is-invalid @enderror" required>
                        <option value="">-- Selecione --</option>
                        @foreach($entityProducts as $ep)
                            @php
                                $entLabel = $ep->entity_type === 'client' ? 'Cliente' : 'Empresa';
                            @endphp
                            <option value="{{ $ep->id }}"
                                    @selected(old('entity_product_id') == $ep->id)>
                                [{{ $entLabel }}] {{ $ep->entity?->name }} -
                                {{ $ep->product?->name }} (Atual: {{ $ep->quantity }})
                            </option>
                        @endforeach
                    </select>
                    @error('entity_product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipo de entrada</label>
                        <input type="text" name="type" id="type"
                               class="form-control @error('type') is-invalid @enderror"
                               value="{{ old('type') }}" placeholder="Ex.: Compra, Devolução...">
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="supplier" class="form-label">Fornecedor</label>
                        <input type="text" name="supplier" id="supplier"
                               class="form-control @error('supplier') is-invalid @enderror"
                               value="{{ old('supplier') }}">
                        @error('supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantidade</label>
                        <input type="number" name="quantity" id="quantity" min="1"
                               class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', 1) }}">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Observações gerais...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('almoxarife.entry_products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Registrar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
