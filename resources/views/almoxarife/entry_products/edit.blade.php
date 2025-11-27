@extends('layouts.almoxarife')

@section('title', 'Editar Entrada')
@section('page-title', 'Editar Entrada de Produto')
@section('page-subtitle', 'Ajustar lançamento de entrada e estoque')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ops!</strong> Verifique os erros abaixo.
                </div>
            @endif

            <div class="mb-3">
                <h6 class="mb-1">
                    Produto: <strong>{{ $entry->entityProduct->product->name ?? '-' }}</strong>
                </h6>
                <div class="small text-muted">
                    Código: {{ $entry->entityProduct->product->code ?? '-' }}<br>
                    Estoque atual: {{ $entry->entityProduct->quantity }}
                </div>
            </div>

            <form action="{{ route('almoxarife.entry_products.update', $entry->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipo de entrada</label>
                        <input type="text" name="type" id="type"
                               class="form-control @error('type') is-invalid @enderror"
                               value="{{ old('type', $entry->type) }}">
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="supplier" class="form-label">Fornecedor</label>
                        <input type="text" name="supplier" id="supplier"
                               class="form-control @error('supplier') is-invalid @enderror"
                               value="{{ old('supplier', $entry->supplier) }}">
                        @error('supplier')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantidade</label>
                        <input type="number" min="1" name="quantity" id="quantity"
                               class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', $entry->quantity) }}">
                        @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $entry->notes) }}</textarea>
                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('almoxarife.entry_products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Atualizar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
