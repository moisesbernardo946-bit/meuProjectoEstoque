@extends('layouts.diretor')

@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('page-subtitle', 'Gestão de clientes da sua empresa')

@section('page-actions')
    <a href="{{ route('diretor.clientes.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo Cliente
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-semibold">Lista de clientes</h6>
                    <small class="text-muted">
                        Empresa: {{ $company->code }} — {{ $company->name }}
                    </small>
                </div>

                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                           placeholder="Pesquisar por nome, código ou NIF">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if($clients->isEmpty())
                <p class="text-muted small p-3 mb-0">
                    Nenhum cliente encontrado.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>NIF</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Estado</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td>{{ $client->code }}</td>
                                <td>{{ $client->name }}</td>
                                <td>{{ $client->nif ?? '-' }}</td>
                                <td>{{ $client->email ?? '-' }}</td>
                                <td>{{ $client->phone ?? '-' }}</td>
                                <td>
                                    @if($client->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('diretor.clientes.edit', $client) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('diretor.clientes.destroy', $client) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-2">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
