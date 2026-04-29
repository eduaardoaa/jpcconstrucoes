<!doctype html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistema EPI')</title>

    <link rel="shortcut icon" href="{{ asset('assets/imgs/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="app-shell sidebar-collapsed" id="appShell">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        @include('partials.sidebar')

        <main class="main-content" id="mainContent">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menu">
                        <i class="bi bi-list"></i>
                    </button>

                    <div>
                        <h1 id="pageTopbarTitle">@yield('pageTitle', 'Painel')</h1>
                        <p id="pageTopbarDescription">@yield('pageDescription', 'Gerencie as informações do sistema.')</p>
                    </div>
                </div>

                <div class="topbar-badge">
                    <span><i class="bi bi-circle-fill"></i></span>
                    <span>Ambiente interno</span>
                </div>
            </header>

            <section class="page-content" id="pageContent">
                @yield('content')
            </section>

            @include('partials.footer')
        </main>
    </div>

    <script>
        const appShell = document.getElementById('appShell');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const pageContent = document.getElementById('pageContent');
        const pageTopbarTitle = document.getElementById('pageTopbarTitle');
        const pageTopbarDescription = document.getElementById('pageTopbarDescription');
        const mobileBreakpoint = 991;

        function isMobile() {
            return window.innerWidth <= mobileBreakpoint;
        }

        function openMobileSidebar() {
            appShell?.classList.add('sidebar-mobile-open');
            document.body.classList.add('sidebar-open-body');
        }

        function closeMobileSidebar() {
            appShell?.classList.remove('sidebar-mobile-open');
            document.body.classList.remove('sidebar-open-body');
        }

        function toggleDesktopSidebar() {
            appShell?.classList.toggle('sidebar-collapsed');
            localStorage.setItem(
                'sidebar-collapsed',
                appShell.classList.contains('sidebar-collapsed') ? 'true' : 'false'
            );
        }

        function applyInitialSidebarState() {
            if (!appShell) return;

            if (localStorage.getItem('sidebar-collapsed') === null) {
                localStorage.setItem('sidebar-collapsed', 'true');
            }

            if (isMobile()) {
                appShell.classList.remove('sidebar-mobile-open');
                document.body.classList.remove('sidebar-open-body');
            } else {
                if (localStorage.getItem('sidebar-collapsed') === 'true') {
                    appShell.classList.add('sidebar-collapsed');
                } else {
                    appShell.classList.remove('sidebar-collapsed');
                }
            }
        }

        function updateActiveSidebarLink(urlString) {
            const currentUrl = new URL(urlString, window.location.origin);
            const currentPath = currentUrl.pathname.replace(/\/+$/, '');
            const sidebarLinks = document.querySelectorAll('.sidebar a.sidebar-link[href]');

            sidebarLinks.forEach(link => {
                const linkUrl = new URL(link.href, window.location.origin);
                const linkPath = linkUrl.pathname.replace(/\/+$/, '');

                const isActive =
                    currentPath === linkPath ||
                    (currentPath.startsWith(linkPath) && linkPath !== '/');

                link.classList.toggle('active', isActive);
            });
        }

        async function navigatePartial(url, pushState = true) {
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Partial-Request': 'true',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    window.location.href = url;
                    return;
                }

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newPageContent = doc.querySelector('#pageContent');
                const newTopbarTitle = doc.querySelector('#pageTopbarTitle');
                const newTopbarDescription = doc.querySelector('#pageTopbarDescription');
                const newTitle = doc.querySelector('title');

                if (!newPageContent) {
                    window.location.href = url;
                    return;
                }

                pageContent.innerHTML = newPageContent.innerHTML;

                if (newTopbarTitle && pageTopbarTitle) {
                    pageTopbarTitle.innerHTML = newTopbarTitle.innerHTML;
                }

                if (newTopbarDescription && pageTopbarDescription) {
                    pageTopbarDescription.innerHTML = newTopbarDescription.innerHTML;
                }

                if (newTitle) {
                    document.title = newTitle.textContent || 'Sistema EPI';
                }

                if (pushState) {
                    window.history.pushState({ url }, '', url);
                }

                updateActiveSidebarLink(url);

                if (isMobile()) {
                    closeMobileSidebar();
                }

                const scripts = pageContent.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');

                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent;
                    }

                    document.body.appendChild(newScript);
                    oldScript.remove();
                });

                document.dispatchEvent(new Event('page:updated'));
            } catch (error) {
                window.location.href = url;
            }
        }

        applyInitialSidebarState();
        updateActiveSidebarLink(window.location.href);

        sidebarToggle?.addEventListener('click', function () {
            if (isMobile()) {
                openMobileSidebar();
            } else {
                toggleDesktopSidebar();
            }
        });

        sidebarClose?.addEventListener('click', closeMobileSidebar);
        sidebarOverlay?.addEventListener('click', closeMobileSidebar);

        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href]');

            if (!link) return;
            if (!link.classList.contains('sidebar-link')) return;
            if (link.target === '_blank') return;
            if (link.hasAttribute('download')) return;
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

            const url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin) return;

            e.preventDefault();
            navigatePartial(url.href, true);
        });

        window.addEventListener('popstate', function () {
            navigatePartial(window.location.href, false);
        });

        window.addEventListener('resize', function () {
            applyInitialSidebarState();
        });
    </script>
</body>
</html>