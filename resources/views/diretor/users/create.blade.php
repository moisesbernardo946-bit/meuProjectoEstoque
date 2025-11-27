@extends('layouts.diretor')

@section('title', 'Novo Usuário')
@section('page-title', 'Novo Usuário')
@section('page-subtitle', 'Cadastrar um novo usuário para esta empresa')

@section('page-actions')
    <a href="{{ route('diretor.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo:
            <ul class="mb-0 mt-1 small">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0 fw-semibold">Dados do usuário</h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('diretor.users.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                            value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control form-control-sm"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Telefone *</label>
                        <input type="tel" name="phone" class="form-control form-control-sm"
                            value="{{ old('phone') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Função / Papel *</label>
                        <select name="role" class="form-select form-select-sm" required>
                            <option value="">Selecione...</option>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Senha *</label>
                        <input type="password" name="password" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Confirmar senha *</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('diretor.users.index') }}" class="btn btn-sm btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
