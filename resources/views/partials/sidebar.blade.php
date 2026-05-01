<aside class="sidebar" id="sidebar">

    <style>
        .sidebar-section {
            margin-top: 10px;
        }

        .sidebar-accordion {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-accordion-item {
            border-radius: 14px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(148, 163, 184, 0.08);
        }

        .sidebar-accordion-toggle {
            width: 100%;
            border: none;
            background: transparent;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 14px;
            cursor: pointer;
            font-weight: 600;
            text-align: left;
            transition: background .2s ease;
        }

        .sidebar-accordion-toggle:hover {
            background: rgba(255, 255, 255, 0.04);
        }

        .sidebar-accordion-toggle.active {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-accordion-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .sidebar-accordion-icon {
            width: 18px;
            display: inline-flex;
            justify-content: center;
            font-size: 16px;
        }

        .sidebar-accordion-label {
            font-size: 14px;
            color: #e2e8f0;
        }

        .sidebar-accordion-arrow {
            transition: transform .2s ease;
            font-size: 13px;
            color: #94a3b8;
        }

        .sidebar-accordion-toggle.active .sidebar-accordion-arrow {
            transform: rotate(180deg);
        }

        .sidebar-accordion-content {
            display: none;
            padding: 4px 8px 10px;
        }

        .sidebar-accordion-content.open {
            display: block;
        }

        .sidebar-subnav {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-subnav .sidebar-link {
            margin-left: 2px;
        }
    </style>

    <div class="sidebar-logo-wrapper">
        <img
            src="{{ asset('assets/imgs/logo.png') }}"
            alt="Logo Sistema EPI"
            class="sidebar-logo"
        >
    </div>

    <div class="sidebar-brand">
        <div class="sidebar-brand__main">
            <div class="sidebar-brand-text">
                <h2>{{ auth()->user()->name ?? 'Usuário' }}</h2>
                <p>{{ auth()->user()->cargo?->nome ?? 'Sem cargo' }}</p>
            </div>
        </div>

        <button type="button" class="sidebar-close-mobile" id="sidebarClose" aria-label="Fechar menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    @php
        $epiOpen =
            request()->routeIs('obras.*') ||
            request()->routeIs('produtos.*') ||
            request()->routeIs('funcionarios.*') ||
            request()->routeIs('estoque.*') ||
            request()->routeIs('entregas.*') ||
            request()->routeIs('epi.*') ||
            request()->routeIs('relatorios.*');

        $abastecimentoOpen =
            request()->routeIs('veiculos.*') ||
            request()->routeIs('abastecimento.admin.*') ||
            request()->routeIs('abastecimento.painel.*') ||
            request()->routeIs('abastecimento.solicitacoes.*') ||
            request()->routeIs('deslocamentos.*');

        $whatsappOpen = request()->routeIs('whatsapp.*');
        $podeWhatsappWeb = auth()->user()->podeAcessarWhatsapp();
        $podeInstancias  = auth()->user()->hasPermissao('gerenciar_whatsapp');
    @endphp

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-grid-1x2-fill"></i></span>
            <span class="sidebar-link-text">Dashboard</span>
        </a>

        @if(auth()->user()->hasPermissao('usuarios'))
            <a href="{{ route('usuarios.index') }}" class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-people-fill"></i></span>
                <span class="sidebar-link-text">Usuários</span>
            </a>
        @endif

        @if(auth()->user()->hasPermissao('cargos'))
            <a href="{{ route('cargos.index') }}" class="sidebar-link {{ request()->routeIs('cargos.*') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i class="bi bi-person-badge-fill"></i></span>
                <span class="sidebar-link-text">Cargos</span>
            </a>
        @endif

        <div class="sidebar-section">
            <div class="sidebar-accordion">

                {{-- SANFONA EPI --}}
                @if(
                    auth()->user()->hasPermissao('obras') ||
                    auth()->user()->hasPermissao('produtos') ||
                    auth()->user()->hasPermissao('funcionarios') ||
                    auth()->user()->hasPermissao('estoque') ||
                    auth()->user()->hasPermissao('entregas_epi') ||
                    auth()->user()->hasPermissao('relatorios')
                )
                    <div class="sidebar-accordion-item">
                        <button
                            type="button"
                            class="sidebar-accordion-toggle {{ $epiOpen ? 'active' : '' }}"
                            onclick="toggleSidebarAccordion('accordion-epi', this)"
                        >
                            <span class="sidebar-accordion-left">
                                <span class="sidebar-accordion-icon">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <span class="sidebar-accordion-label">EPI</span>
                            </span>

                            <span class="sidebar-accordion-arrow">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </button>

                        <div id="accordion-epi" class="sidebar-accordion-content {{ $epiOpen ? 'open' : '' }}">
                            <div class="sidebar-subnav">
                                @if(auth()->user()->hasPermissao('obras'))
                                    <a href="{{ route('obras.index') }}" class="sidebar-link {{ request()->routeIs('obras.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-building"></i></span>
                                        <span class="sidebar-link-text">Obras</span>
                                    </a>
                                @endif

                                @if(auth()->user()->hasPermissao('produtos'))
                                    <a href="{{ route('produtos.index') }}" class="sidebar-link {{ request()->routeIs('produtos.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-box-seam"></i></span>
                                        <span class="sidebar-link-text">Produtos</span>
                                    </a>
                                @endif

                                @if(auth()->user()->hasPermissao('funcionarios'))
                                    <a href="{{ route('funcionarios.index') }}" class="sidebar-link {{ request()->routeIs('funcionarios.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-person-workspace"></i></span>
                                        <span class="sidebar-link-text">Funcionários</span>
                                    </a>
                                @endif

                                @if(auth()->user()->hasPermissao('estoque'))
                                    <a href="{{ route('estoque.index') }}" class="sidebar-link {{ request()->routeIs('estoque.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-boxes"></i></span>
                                        <span class="sidebar-link-text">Estoque</span>
                                    </a>
                                @endif

                                @if(auth()->user()->hasPermissao('entregas_epi'))
                                    <a href="{{ route('entregas.index') }}" class="sidebar-link {{ request()->routeIs('entregas.*') || request()->routeIs('epi.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-shield-check"></i></span>
                                        <span class="sidebar-link-text">Entregas de EPI</span>
                                    </a>
                                @endif

                                @if(auth()->user()->hasPermissao('relatorios'))
                                    <a href="{{ route('relatorios.index') }}" class="sidebar-link {{ request()->routeIs('relatorios.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-bar-chart-line-fill"></i></span>
                                        <span class="sidebar-link-text">Relatórios</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- SANFONA ABASTECIMENTO --}}
                @if(
                    auth()->user()->hasPermissao('gerenciamento_combustivel') ||
                    auth()->user()->podeSolicitarAbastecimento() ||
                    auth()->user()->hasPermissao('deslocamentos')
                )
                    <div class="sidebar-accordion-item">
                        <button
                            type="button"
                            class="sidebar-accordion-toggle {{ $abastecimentoOpen ? 'active' : '' }}"
                            onclick="toggleSidebarAccordion('accordion-abastecimento', this)"
                        >
                            <span class="sidebar-accordion-left">
                                <span class="sidebar-accordion-icon">
                                    <i class="bi bi-fuel-pump-fill"></i>
                                </span>
                                <span class="sidebar-accordion-label">Abastecimento</span>
                            </span>

                            <span class="sidebar-accordion-arrow">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </button>

                        <div id="accordion-abastecimento" class="sidebar-accordion-content {{ $abastecimentoOpen ? 'open' : '' }}">
                            <div class="sidebar-subnav">
                                @if(auth()->user()->hasPermissao('gerenciamento_combustivel'))
                                    <a href="{{ route('veiculos.index') }}" class="sidebar-link {{ request()->routeIs('veiculos.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-car-front-fill"></i></span>
                                        <span class="sidebar-link-text">Veículos</span>
                                    </a>

                                    <a href="{{ route('abastecimento.painel.index') }}" class="sidebar-link {{ request()->routeIs('abastecimento.painel.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-graph-up-arrow"></i></span>
                                        <span class="sidebar-link-text">Painel combustível</span>
                                    </a>

                                    <a href="{{ route('abastecimento.admin.index') }}" class="sidebar-link {{ request()->routeIs('abastecimento.admin.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-clipboard-data-fill"></i></span>
                                        <span class="sidebar-link-text">Solicitações combustível</span>
                                    </a>
                                @endif

                                @if(auth()->user()->podeSolicitarAbastecimento())
                                    <a href="{{ route('abastecimento.solicitacoes.index') }}" class="sidebar-link {{ request()->routeIs('abastecimento.solicitacoes.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-fuel-pump"></i></span>
                                        <span class="sidebar-link-text">Solicitar abastecimento</span>
                                    </a>
                                @endif

                                @if(auth()->user()->hasPermissao('deslocamentos'))
    <a href="{{ route('deslocamentos.index') }}" class="sidebar-link {{ request()->routeIs('deslocamentos.index') ? 'active' : '' }}">
        <span class="sidebar-link-icon"><i class="bi bi-map-fill"></i></span>
        <span class="sidebar-link-text">Controle de deslocamento</span>
    </a>
@endif

@if(auth()->user()->podeSolicitarAbastecimento())
    <a href="{{ route('deslocamentos.meus') }}" class="sidebar-link {{ request()->routeIs('deslocamentos.meus') ? 'active' : '' }}">
        <span class="sidebar-link-icon"><i class="bi bi-sign-turn-right-fill"></i></span>
        <span class="sidebar-link-text">Meus deslocamentos</span>
    </a>
@endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- SANFONA WHATSAPP --}}
                @if($podeWhatsappWeb || $podeInstancias)
                    <div class="sidebar-accordion-item">
                        <button
                            type="button"
                            class="sidebar-accordion-toggle {{ $whatsappOpen ? 'active' : '' }}"
                            onclick="toggleSidebarAccordion('accordion-whatsapp', this)"
                        >
                            <span class="sidebar-accordion-left">
                                <span class="sidebar-accordion-icon">
                                    <i class="bi bi-whatsapp"></i>
                                </span>
                                <span class="sidebar-accordion-label">WhatsApp</span>
                            </span>
                            <span class="sidebar-accordion-arrow">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </button>

                        <div id="accordion-whatsapp" class="sidebar-accordion-content {{ $whatsappOpen ? 'open' : '' }}">
                            <div class="sidebar-subnav">
                                @if($podeWhatsappWeb)
                                    <a href="{{ route('whatsapp.conversas.index') }}" class="sidebar-link {{ request()->routeIs('whatsapp.conversas.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-chat-dots-fill"></i></span>
                                        <span class="sidebar-link-text">WhatsApp Web</span>
                                    </a>
                                @endif

                                @if($podeInstancias)
                                    <a href="{{ route('whatsapp.instancias.index') }}" class="sidebar-link {{ request()->routeIs('whatsapp.instancias.*') ? 'active' : '' }}">
                                        <span class="sidebar-link-icon"><i class="bi bi-phone-fill"></i></span>
                                        <span class="sidebar-link-text">Instâncias</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('perfil.edit') }}" class="sidebar-link {{ request()->routeIs('perfil.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="bi bi-person-circle"></i></span>
            <span class="sidebar-link-text">Editar perfil</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span class="sidebar-link-text">Sair do sistema</span>
            </button>
        </form>
    </div>

    <script>
        function toggleSidebarAccordion(contentId, button) {
            const content = document.getElementById(contentId);
            if (!content) return;

            content.classList.toggle('open');
            button.classList.toggle('active');
        }
    </script>

</aside>