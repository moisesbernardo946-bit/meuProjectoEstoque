@extends('layouts.diretor')

@section('title', 'Perfil')
@section('page-title', 'Perfil do usuário')
@section('page-subtitle', 'Gerencie seus dados de acesso')

@section('content')

    {{-- Alerts de perfil --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alerts de senha --}}
    @if (session('success_password'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success_password') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error_password'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error_password') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        {{-- Dados básicos --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person-circle me-1"></i> Dados do perfil
                    </h6>
                    <small class="text-muted">Atualize seu nome e email.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('diretor.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Salvar alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Empresa / Grupo e alteração de senha --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-buildings me-1"></i> Vínculo com empresa
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Grupo</dt>
                        <dd class="col-sm-8">
                            {{ $group?->name ?? 'Não definido' }}
                        </dd>

                        <dt class="col-sm-4">Empresa</dt>
                        <dd class="col-sm-8">
                            @if ($company)
                                {{ $company->code }} — {{ $company->name }}
                            @else
                                <span class="text-muted">Nenhuma empresa associada</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Função</dt>
                        <dd class="col-sm-8 text-capitalize">
                            {{ $user->role }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-shield-lock me-1"></i> Alterar senha
                    </h6>
                    <small class="text-muted">Recomendada senha com pelo menos 8 caracteres.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('diretor.profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Senha atual</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nova senha</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar nova senha</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-key me-1"></i> Atualizar senha
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
