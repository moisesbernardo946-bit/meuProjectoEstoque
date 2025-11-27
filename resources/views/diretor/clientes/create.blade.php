@extends('layouts.diretor')

@section('title', 'Novo Cliente')
@section('page-title', 'Novo Cliente')
@section('page-subtitle', 'Cadastro de cliente para a sua empresa')

@section('page-actions')
    <a href="{{ route('diretor.clientes.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
@endsection

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo:
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0 fw-semibold">
                Dados do cliente
            </h6>
            <small class="text-muted">
                Empresa: {{ $company->code }} — {{ $company->name }}
            </small>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('diretor.clientes.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Código *</label>
                        <input type="text" name="code" class="form-control form-control-sm"
                               value="{{ old('code') }}" required>
                    </div>

                    <div class="col-md-9">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">NIF</label>
                        <input type="text" name="nif" class="form-control form-control-sm"
                               value="{{ old('nif') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm"
                               value="{{ old('email') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="phone" class="form-control form-control-sm"
                               value="{{ old('phone') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="address" class="form-control form-control-sm"
                               value="{{ old('address') }}">
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="is_active" id="is_active"
                                   value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Cliente ativo
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('diretor.clientes.index') }}" class="btn btn-sm btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
