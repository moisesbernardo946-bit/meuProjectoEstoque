<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Painel do Almoxarife') - ERP Grupo Terra</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Ícones Bootstrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .almox-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .almox-main {
            flex: 1;
            display: flex;
            min-height: 0;
        }

        .almox-sidebar {
            width: 250px;
            background: #0f172a;
            color: #e5e7eb;
        }

        .almox-sidebar .sidebar-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.3);
        }

        .almox-sidebar .sidebar-header h5 {
            font-size: 1rem;
            margin: 0;
        }

        .almox-sidebar .sidebar-header small {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .almox-sidebar a {
            color: #e5e7eb;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .almox-sidebar a:hover,
        .almox-sidebar a.active {
            background: rgba(148, 163, 184, 0.15);
        }

        .almox-sidebar .nav-link {
            padding: 0.65rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .almox-content {
            flex: 1;
            padding: 1.5rem;
            overflow-x: hidden;
        }

        .almox-page-header {
            margin-bottom: 1rem;
        }

        .almox-page-header h4 {
            margin: 0;
        }

        .almox-page-header small {
            color: #6b7280;
        }

        footer.almox-footer {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        @media (min-width: 768px) {
            .almox-main {
                flex-direction: row;
            }
        }

        @media (max-width: 767.98px) {
            .almox-content {
                padding: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="almox-wrapper">
    {{-- NAVBAR SUPERIOR --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm-sm">
        <div class="container-fluid">
            {{-- BOTÃO HAMBÚRGUER (MOBILE) --}}
            <button class="btn btn-outline-secondary d-md-none me-2" type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#almoxSidebarMobile"
                    aria-controls="almoxSidebarMobile">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <i class="bi bi-box-seam text-primary"></i>
                <span class="fw-bold">ERP Grupo Terra</span>
            </a>

            <div class="d-flex align-items-center ms-auto">
                @php
                    $user   = auth()->user();
                    $empresa = $user?->company;
                    $grupo   = $empresa?->group;
                @endphp

                @if($empresa)
                    <div class="text-end me-3 d-none d-md-block">
                        <div class="small text-muted">
                            {{ $grupo?->name ?? 'Grupo não definido' }}
                        </div>
                        <div class="fw-semibold">
                            {{ $empresa->name }}
                        </div>
                    </div>
                @endif

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-inline">{{ $user?->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header small">
                            {{ $user?->email }}
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('almoxarife.almoxarife_profile') }}">
                                <i class="bi bi-gear me-1"></i> Perfil
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i> Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    {{-- CONTEÚDO PRINCIPAL --}}
    <div class="almox-main">
        {{-- SIDEBAR FIXO (DESKTOP) --}}
        <aside class="almox-sidebar d-none d-md-block">
            <div class="sidebar-header">
                <h5 class="fw-semibold">
                    Painel Almoxarife
                </h5>
                <small>
                    @if($empresa)
                        {{ $empresa->code }} — {{ $empresa->name }}
                    @else
                        Nenhuma empresa associada
                    @endif
                </small>
            </div>

            <nav class="nav flex-column mt-2 mb-3">
                <a class="nav-link {{ request()->routeIs('almoxarife.dashboard') ? 'active' : '' }}"
                   href="{{ route('almoxarife.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.categories.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.categories.index') }}">
                    <i class="bi bi-tags"></i>
                    <span>Categorias</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.units.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.units.index') }}">
                    <i class="bi bi-rulers"></i>
                    <span>Unidades</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.zones.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.zones.index') }}">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span>Zonas</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.products.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.products.index') }}">
                    <i class="bi bi-box"></i>
                    <span>Produtos</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.requisitions.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.requisitions.index') }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Requisições</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.entity_products.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.entity_products.index') }}">
                    <i class="bi bi-boxes"></i>
                    <span>Estoque (Entity)</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.entry_products.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.entry_products.index') }}">
                    <i class="bi bi-box-arrow-in-down"></i>
                    <span>Entradas</span>
                </a>

                <a class="nav-link {{ request()->routeIs('almoxarife.exit_products.*') ? 'active' : '' }}"
                   href="{{ route('almoxarife.exit_products.index') }}">
                    <i class="bi bi-box-arrow-up"></i>
                    <span>Saídas</span>
                </a>
            </nav>
        </aside>

        {{-- SIDEBAR MOBILE (OFFCANVAS) --}}
        <div class="offcanvas offcanvas-start text-bg-dark d-md-none"
             tabindex="-1" id="almoxSidebarMobile"
             aria-labelledby="almoxSidebarMobileLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="almoxSidebarMobileLabel">Painel Almoxarife</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <div class="sidebar-header px-3 pt-2 pb-2 border-bottom border-secondary">
                    <small>
                        @if($empresa)
                            {{ $empresa->code }} — {{ $empresa->name }}<br>
                            {{ $grupo?->name ?? 'Grupo não definido' }}
                        @else
                            Nenhuma empresa associada
                        @endif
                    </small>
                </div>

                <nav class="nav flex-column mt-2 mb-3" id="almoxSidebarMobileNav">
                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.dashboard') ? 'active' : '' }}"
                       href="{{ route('almoxarife.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>Dashboard</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.categories.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.categories.index') }}">
                        <i class="bi bi-tags me-2"></i>
                        <span>Categorias</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.units.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.units.index') }}">
                        <i class="bi bi-rulers me-2"></i>
                        <span>Unidades</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.zones.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.zones.index') }}">
                        <i class="bi bi-grid-3x3-gap me-2"></i>
                        <span>Zonas</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.products.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.products.index') }}">
                        <i class="bi bi-box me-2"></i>
                        <span>Produtos</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.requisitions.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.requisitions.index') }}">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <span>Requisições</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.entity_products.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.entity_products.index') }}">
                        <i class="bi bi-boxes me-2"></i>
                        <span>Estoque (Entity)</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.entry_products.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.entry_products.index') }}">
                        <i class="bi bi-box-arrow-in-down me-2"></i>
                        <span>Entradas</span>
                    </a>

                    <a class="nav-link px-3 py-2 {{ request()->routeIs('almoxarife.exit_products.*') ? 'active' : '' }}"
                       href="{{ route('almoxarife.exit_products.index') }}">
                        <i class="bi bi-box-arrow-up me-2"></i>
                        <span>Saídas</span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- ÁREA DE CONTEÚDO --}}
        <main class="almox-content">
            <div class="almox-page-header d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold">
                        @yield('page-title', 'Dashboard do Almoxarife')
                    </h4>
                    <small>
                        @yield('page-subtitle', 'Visão geral do estoque da sua empresa')
                    </small>
                </div>

                <div>
                    @yield('page-actions')
                </div>
            </div>

            @yield('content')
        </main>
    </div>

    {{-- Rodapé --}}
    <footer class="almox-footer">
        <div class="d-flex justify-content-between">
            <span>© {{ date('Y') }} - ERP Grupo Terra</span>
            <span class="text-muted">Almoxarife - Módulo de Estoque</span>
        </div>
    </footer>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const offcanvasElement = document.getElementById('almoxSidebarMobile');
        const offcanvasInstance = offcanvasElement ? bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement) : null;

        if (offcanvasInstance) {
            const nav = document.getElementById('almoxSidebarMobileNav');
            if (nav) {
                nav.querySelectorAll('a.nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        offcanvasInstance.hide();
                    });
                });
            }
        }
    });
</script>

@stack('scripts')
</body>
</html>
