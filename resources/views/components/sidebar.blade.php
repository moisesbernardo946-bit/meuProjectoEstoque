<div class="sidebar">
    <div class="text-center py-3 border-bottom">
        <h5 class="m-0 fw-bold">Grupo Terra</h5>
        <small class="text-muted">Gestão de Estoque</small>
    </div>

    <nav class="nav flex-column mt-3">
        <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}">
            <i class="bi bi-tags"></i>
            <span>Categorias</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-box-seam"></i>
            <span>Produtos</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-arrow-down-circle"></i>
            <span>Entradas</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-arrow-up-circle"></i>
            <span>Saídas</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-journal-text"></i>
            <span>Requerimentos</span>
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-people"></i>
            <span>Clientes</span>
        </a>
    </nav>
</div>
