@extends('layouts.diretor')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard do Diretor')
@section('page-subtitle', 'Visão geral da sua empresa')

@section('content')
    {{-- Linha de boas-vindas --}}
    <div class="mb-4">
        <h5 class="fw-semibold mb-1">
            Olá, {{ $user->name }} 👋
        </h5>
        <p class="text-muted mb-0">
            Bem-vindo ao painel da empresa
            <strong>{{ $company?->name ?? '—' }}</strong>
            @if ($group)
                (Grupo: <strong>{{ $group->name }}</strong>)
            @endif
        </p>
    </div>

    {{-- Cards de resumo rápido --}}
    <div class="row g-3 mb-4">
        {{-- Clientes --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase">Clientes</span>
                        <i class="bi bi-person-vcard fs-4 text-primary"></i>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold">{{ $totalClientes ?? 0 }}</h3>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('diretor.clientes.index') }}" class="small text-decoration-none">
                            Ver lista de clientes <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Usuários --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase">Usuários</span>
                        <i class="bi bi-people fs-4 text-success"></i>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold">{{ $totalUsers ?? 0 }}</h3>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('diretor.users.index') }}" class="small text-decoration-none">
                            Gerir usuários <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Requisições (todas) --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase">Requisições</span>
                        <i class="bi bi-file-earmark-text fs-4 text-warning"></i>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold">{{ $totalRequisicoes ?? 0 }}</h3>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('diretor.requisitions.index') }}" class="small text-decoration-none">
                            Ver requisições <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Requisições pendentes de aprovação --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase">Pendentes</span>
                        <i class="bi bi-hourglass-split fs-4 text-danger"></i>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold">{{ $totalRequisicoesPendentes ?? 0 }}</h3>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('diretor.requisitions.index', ['status' => 'pendente']) }}"
                            class="small text-decoration-none text-danger">
                            Ver pendentes <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha de 2 colunas: visão da empresa + últimas requisições --}}
    <div class="row g-3">
        {{-- Informações da empresa --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-buildings me-1"></i> Informações da Empresa
                    </h6>
                </div>
                <div class="card-body">
                    @if ($company)
                        <div class="mb-2">
                            <span class="text-muted small d-block">Empresa</span>
                            <span class="fw-semibold">{{ $company->code }} — {{ $company->name }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Grupo</span>
                            <span>{{ $group?->name ?? '—' }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">NIF</span>
                            <span>{{ $company->nif ?? '—' }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Email</span>
                            <span>{{ $company->email ?? '—' }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Telefone</span>
                            <span>{{ $company->phone ?? '—' }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Endereço</span>
                            <span>{{ $company->address ?? '—' }}</span>
                        </div>
                    @else
                        <p class="text-muted mb-0">
                            Nenhuma empresa associada ao seu usuário.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Últimas requisições --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text me-1"></i> Últimas requisições
                    </h6>
                    <a href="{{ route('diretor.requisitions.index') }}" class="small text-decoration-none">
                        Ver todas <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>

                <div class="card-body p-0">
                    @if ($ultimasRequisicoes->isEmpty())
                        <p class="text-muted small p-3 mb-0">
                            Nenhuma requisição encontrada para esta empresa.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Criada por</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ultimasRequisicoes as $req)
                                        <tr>
                                            <td>{{ $req->id }}</td>
                                            <td>{{ $req->client?->name ?? '-' }}</td>
                                            <td>{{ $req->user?->name ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $status = $req->status;
                                                    $badgeClass = match ($status) {
                                                        'pendente' => 'bg-warning text-dark',
                                                        'aprovada' => 'bg-success',
                                                        'rejeitada' => 'bg-danger',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $req->created_at?->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
