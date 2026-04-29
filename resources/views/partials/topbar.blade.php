<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menu">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-title-group">
            <h1>@yield('pageTitle', $title ?? 'Painel')</h1>
            <p>@yield('pageDescription', 'Gerencie as informações do sistema.')</p>
        </div>
    </div>

    <div class="topbar-right">
        <div class="topbar-badge">
            <span class="topbar-badge__dot"><i class="bi bi-circle-fill"></i></span>
            <span>Ambiente interno</span>
        </div>
    </div>
</header>