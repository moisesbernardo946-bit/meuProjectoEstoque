<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <button id="toggleSidebar" class="btn btn-outline-secondary d-none d-md-inline">
            <i class="bi bi-list"></i>
        </button>

        <button id="mobileMenu" class="btn btn-outline-secondary d-md-none me-2">
            <i class="bi bi-list"></i>
        </button>

        <a class="navbar-brand ms-2 fw-bold" href="{{ route('dashboard.index') }}">Grupo Terra</a>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? 'Usuário' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Sair</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
