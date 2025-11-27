@extends('layouts.diretor')

@section('title', 'Usuários')
@section('page-title', 'Usuários da empresa')
@section('page-subtitle', $company?->name ?? 'Empresa não definida')

@section('page-actions')
    <a href="{{ route('diretor.users.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Novo usuário
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 align-items-md-center">
                <div>
                    <h6 class="mb-0 fw-semibold">Lista de usuários</h6>
                    <small class="text-muted">Gestão de usuários da sua empresa</small>
                </div>

                {{-- 🔍 FILTROS --}}
                <form method="GET" action="{{ route('diretor.users.index') }}" class="d-flex flex-wrap gap-2">
                    {{-- busca por nome/email --}}
                    <div class="input-group input-group-sm" style="min-width: 220px;">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Buscar por nome ou email..."
                               value="{{ request('search') }}">
                    </div>

                    {{-- filtro por role --}}
                    <select name="role" class="form-select form-select-sm" style="min-width: 160px;">
                        <option value="">-- Todos os perfis --</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ request('role') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>

                    @if(request('search') || request('role'))
                        <a href="{{ route('diretor.users.index') }}" class="btn btn-sm btn-outline-secondary">
                            Limpar
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Função</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $user->name }}</span>
                        </td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-secondary text-capitalize">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('diretor.users.edit', $user) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>

                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('diretor.users.destroy', $user) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            Nenhum usuário encontrado para estes filtros.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-white border-0">
                {{-- mantém os filtros na paginação --}}
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
