@extends('layouts.almoxarife')

@section('title', 'Editar Saída')
@section('page-title', 'Editar Saída de Produto')
@section('page-subtitle', 'Ajustar lançamento de saída e estoque')

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
                    Produto: <strong>{{ $exit->entityProduct->product->name ?? '-' }}</strong>
                </h6>
                <div class="small text-muted">
                    Código: {{ $exit->entityProduct->product->code ?? '-' }}<br>
                    Estoque atual: {{ $exit->entityProduct->quantity }}
                </div>
            </div>

            <form action="{{ route('almoxarife.exit_products.update', $exit->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label">Tipo de saída</label>
                        <input type="text" name="type" id="type"
                               class="form-control @error('type') is-invalid @enderror"
                               value="{{ old('type', $exit->type) }}">
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantidade</label>
                        <input type="number" min="1" name="quantity" id="quantity"
                               class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', $exit->quantity) }}">
                        @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $exit->notes) }}</textarea>
                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('almoxarife.exit_products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Atualizar Saída
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
