@extends('layouts.diretor')

@section('title', 'Empresas')
@section('page-title', 'Empresa')
@section('page-subtitle', 'Informações da empresa à qual você pertence')

@section('page-actions')
    @if ($company)
        <a href="{{ route('diretor.companies.edit', $company) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i> Editar dados
        </a>
    @endif
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (!$company)
        <div class="alert alert-warning">
            Nenhuma empresa associada ao seu usuário.
        </div>
    @else
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-semibold">
                        Dados da empresa
                    </h6>
                    <small class="text-muted">
                        Grupo: {{ $group?->name ?? 'Sem grupo' }}
                    </small>
                </div>
                <span class="badge bg-light text-muted">
                    Código: {{ $company->code }}
                </span>
            </div>

            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 small text-muted">Nome</dt>
                    <dd class="col-sm-9 fw-semibold">{{ $company->name }}</dd>

                    <dt class="col-sm-3 small text-muted">NIF</dt>
                    <dd class="col-sm-9">{{ $company->nif ?? '-' }}</dd>

                    <dt class="col-sm-3 small text-muted">Email</dt>
                    <dd class="col-sm-9">{{ $company->email ?? '-' }}</dd>

                    <dt class="col-sm-3 small text-muted">Telefone</dt>
                    <dd class="col-sm-9">{{ $company->phone ?? '-' }}</dd>

                    <dt class="col-sm-3 small text-muted">Endereço</dt>
                    <dd class="col-sm-9">{{ $company->address ?? '-' }}</dd>

                    <dt class="col-sm-3 small text-muted">Estado</dt>
                    <dd class="col-sm-9">
                        @if ($company->is_active)
                            <span class="badge bg-success">Ativa</span>
                        @else
                            <span class="badge bg-secondary">Inativa</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    @endif
@endsection
