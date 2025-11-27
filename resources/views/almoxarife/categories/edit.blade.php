@extends('layouts.almoxarife')

@section('title', 'Editar Categoria')
@section('page-title', 'Editar Categoria')
@section('page-subtitle', 'Atualizar dados da categoria')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('almoxarife.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $category->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('almoxarife.categories.index') }}" class="btn btn-light border">
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
