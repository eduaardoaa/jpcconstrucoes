@extends('layouts.app')

@section('title', 'Controle de EPI')
@section('pageTitle', 'Controle de EPI')
@section('pageDescription', 'Gerencie funcionários e acompanhe o histórico mais recente de EPI.')

@section('content')
    <style>
        :root {
            --accent: #10b981;
            --accent-2: #059669;
            --accent-soft: rgba(16, 185, 129, 0.10);
            --accent-ring: rgba(16, 185, 129, 0.18);

            --neutral-1: #0b1220;
            --neutral-2: #0f172a;
            --neutral-3: #111827;
            --border: rgba(148, 163, 184, 0.16);
            --border-soft: rgba(148, 163, 184, 0.10);
            --border-strong: rgba(148, 163, 184, 0.24);

            --text: #e2e8f0;
            --text-strong: #f8fafc;
            --text-muted: #94a3b8;
            --text-subtle: #64748b;

            --success: #10b981;
            --success-soft: rgba(16, 185, 129, 0.12);
            --warning: #f59e0b;
            --warning-soft: rgba(245, 158, 11, 0.12);
            --danger: #ef4444;
            --danger-soft: rgba(239, 68, 68, 0.12);
            --info: #94a3b8;
            --info-soft: rgba(148, 163, 184, 0.12);

            --radius-lg: 18px;
            --radius-md: 14px;
            --radius-sm: 10px;

            --ease: cubic-bezier(.22, .61, .36, 1);
            --t-fast: 180ms;
            --t-med: 260ms;
            --t-slow: 380ms;
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 14px;
            padding: 0;
            background: transparent;
            border: none;
        }

        .page-head__left {
            display: flex;
            gap: 14px;
            align-items: center;
            min-width: 0;
        }

        .page-head__icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.22), rgba(16, 185, 129, 0.08));
            color: #6ee7b7;
            font-size: 20px;
            flex-shrink: 0;
            border: 1px solid rgba(16, 185, 129, 0.18);
        }

        .page-head__text h2 {
            margin: 0;
            font-size: 19px;
            color: var(--text-strong);
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .page-head__text p {
            margin: 3px 0 0;
            font-size: 13.5px;
            color: var(--text-muted);
        }

        .actions-inline {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 14px;
            border-radius: 11px;
            border: 1px solid transparent;
            cursor: pointer;
            line-height: 1;
            white-space: nowrap;
            transition:
                transform var(--t-fast) var(--ease),
                box-shadow var(--t-med) var(--ease),
                background-color var(--t-med) var(--ease),
                border-color var(--t-med) var(--ease),
                color var(--t-med) var(--ease);
            text-decoration: none;
        }

        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        .btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px var(--accent-ring);
        }

        .btn-green {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.22);
        }

        .btn-green:hover {
            box-shadow: 0 10px 24px rgba(16, 185, 129, 0.32);
        }

        .btn-dark {
            background: rgba(15, 23, 42, 0.72);
            border-color: var(--border-strong);
            color: var(--text);
        }

        .btn-dark:hover {
            background: rgba(30, 41, 59, 0.92);
            border-color: rgba(148, 163, 184, 0.34);
        }

        .alert-error-box,
        .alert-success-box {
            border-radius: var(--radius-md);
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 14px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .alert-error-box {
            background: var(--danger-soft);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.22);
        }

        .alert-success-box {
            background: var(--success-soft);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.22);
        }

        .card {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(4px);
        }

        .card-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.015);
        }

        .card-title {
            color: var(--text-strong);
            font-weight: 700;
            font-size: 16px;
        }

        .card-subtitle {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 2px;
        }

        .card-count {
            font-size: 12px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.08);
            color: var(--text-muted);
            border: 1px solid var(--border-soft);
        }

        .card-body {
            padding: 0;
            background: transparent;
        }

        .filters-wrap {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.015);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .form-label i {
            color: var(--accent);
            font-size: 12.5px;
            opacity: 0.85;
        }

        .form-control-custom {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border-radius: 11px;
            border: 1px solid var(--border-strong);
            background: #0b1220;
            color: var(--text-strong);
            font-size: 14.5px;
            outline: none;
            transition: border-color var(--t-med) var(--ease), box-shadow var(--t-med) var(--ease), background-color var(--t-med) var(--ease);
            appearance: none;
            -webkit-appearance: none;
        }

        .form-control-custom::placeholder {
            color: var(--text-subtle);
        }

        .form-control-custom:hover {
            border-color: rgba(148, 163, 184, 0.34);
        }

        .form-control-custom:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-ring);
            background: #111827;
        }

        select.form-control-custom {
            padding-right: 40px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
            background-position: right 14px center;
            background-repeat: no-repeat;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            background: transparent;
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: rgba(15, 23, 42, 0.72);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 11.5px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table tbody td {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border-soft);
            color: var(--text);
            vertical-align: top;
            background: transparent;
            transition: background-color var(--t-med) var(--ease);
        }

        .table tbody tr:hover td {
            background: rgba(255, 255, 255, 0.025);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(148, 163, 184, 0.22), rgba(148, 163, 184, 0.08));
            color: var(--text-strong);
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid var(--border-strong);
            text-transform: uppercase;
        }

        .user-cell__name {
            font-weight: 600;
            color: var(--text-strong);
            line-height: 1.2;
        }

        .user-cell__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            border: 1px solid transparent;
            line-height: 1.2;
        }

        .badge-status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .badge-success {
            background: var(--success-soft);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.22);
        }

        .badge-warning {
            background: var(--warning-soft);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.22);
        }

        .badge-info {
            background: var(--info-soft);
            color: #cbd5e1;
            border-color: rgba(148, 163, 184, 0.18);
        }

        .simple-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 260px;
        }

        .simple-item {
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.05);
        }

        .simple-item__title {
            font-weight: 600;
            color: var(--text-strong);
            line-height: 1.2;
        }

        .text-muted-small {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .table-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 42px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 38px;
            color: var(--border-strong);
            display: block;
            margin-bottom: 10px;
        }

        .empty-state__title {
            color: var(--text-strong);
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
        }

        @media (max-width: 820px) {
            .page-head {
                flex-direction: column;
                align-items: stretch;
            }

            .page-head__left {
                width: 100%;
            }

            .page-head .actions-inline {
                width: 100%;
            }

            .page-head .actions-inline .btn {
                width: 100%;
            }

            .card-header {
                padding: 14px 16px;
                flex-wrap: wrap;
            }

            .filters-wrap {
                padding: 14px 16px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .table thead {
                display: none;
            }

            .table,
            .table tbody,
            .table tr,
            .table td {
                display: block;
                width: 100%;
            }

            .table tbody {
                padding: 14px;
            }

            .table tr {
                background: rgba(255, 255, 255, 0.015);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 14px;
                margin-bottom: 12px;
                position: relative;
            }

            .table tr td {
                border: none !important;
                padding: 6px 0 !important;
                text-align: left !important;
                background: transparent !important;
            }

            .table td[data-label]::before {
                content: attr(data-label);
                display: block;
                font-size: 10.5px;
                font-weight: 700;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: var(--text-muted);
                margin-bottom: 2px;
            }

            .table-actions {
                flex-direction: column;
                width: 100%;
            }

            .table-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .simple-list {
                min-width: 100%;
            }
        }
    </style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-shield-check"></i></div>
            <div class="page-head__text">
                <h2>Controle de EPI</h2>
                <p>Visualize todos os funcionários, a última entrega realizada e o status do comprovante.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('entregas.index', ['open_create' => 1]) }}" class="btn btn-green">
                <i class="bi bi-plus-circle"></i>
                Nova Entrega
            </a>

            <a href="{{ route('funcionarios.index') }}" class="btn btn-dark">
                <i class="bi bi-people"></i>
                Funcionários
            </a>

            <a href="{{ route('entregas.index') }}" class="btn btn-dark">
                <i class="bi bi-box-seam"></i>
                Entregas
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <div>
                <div class="card-title">Filtros</div>
                <div class="card-subtitle">Pesquise funcionários e refine por cargo, obra e comprovante.</div>
            </div>
        </div>

        <div class="filters-wrap" style="border-bottom:none;">
            <form method="GET" action="{{ route('epi.index') }}" id="formFiltrosEpi">
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label class="form-label">
                            <i class="bi bi-search"></i>
                            Pesquisar funcionário
                        </label>
                        <input
                            type="text"
                            name="search"
                            id="filtroBuscaFuncionarioEpi"
                            class="form-control-custom"
                            placeholder="Digite o nome, matrícula ou CPF"
                            value="{{ $search }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-briefcase"></i>
                            Cargo
                        </label>
                        <select name="cargo_id" id="filtroCargoEpi" class="form-control-custom">
                            <option value="">Todos</option>
                            @foreach($cargos as $cargo)
                                <option value="{{ $cargo->id }}" {{ (string) $cargoId === (string) $cargo->id ? 'selected' : '' }}>
                                    {{ $cargo->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-building"></i>
                            Obra
                        </label>
                        <select name="obra_id" id="filtroObraEpi" class="form-control-custom">
                            <option value="">Todas</option>
                            @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" {{ (string) $obraId === (string) $obra->id ? 'selected' : '' }}>
                                    {{ $obra->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-paperclip"></i>
                            Comprovante
                        </label>
                        <select name="status_comprovante" id="filtroComprovanteEpi" class="form-control-custom">
                            <option value="">Todos</option>
                            <option value="pendente" {{ $statusComprovante === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="anexado" {{ $statusComprovante === 'anexado' ? 'selected' : '' }}>Anexado</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success-box">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Lista de Funcionários</div>
                <div class="card-subtitle">Todos os funcionários, com foco na última entrega realizada.</div>
            </div>

            <span class="card-count">
                {{ $funcionarios->count() }} {{ $funcionarios->count() === 1 ? 'funcionário' : 'funcionários' }}
            </span>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Obra</th>
                            <th>Cargo</th>
                            <th>Última entrega</th>
                            <th>Itens da última entrega</th>
                            <th>Último comprovante</th>
                                                        <th style="text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funcionarios as $funcionario)
                            @php
                                $ultimaEntrega = $funcionario->ultima_entrega;
                                $ultimoComprovante = $funcionario->ultimo_comprovante;

                                $iniciais = collect(explode(' ', trim($funcionario->nome)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_substr($p, 0, 1))
                                    ->implode('');
                            @endphp

                            <tr>
                                <td data-label="Funcionário">
                                    <div class="user-cell">
                                        <div class="avatar">{{ $iniciais ?: 'F' }}</div>
                                        <div>
                                            <div class="user-cell__name">{{ $funcionario->nome }}</div>
                                            <div class="user-cell__sub">Matrícula: {{ $funcionario->matricula ?: '-' }}</div>
                                            <div class="user-cell__sub">CPF: {{ $funcionario->cpf ?: '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Obra">
                                    {{ $funcionario->obra->nome ?? '-' }}
                                </td>

                                <td data-label="Cargo">
                                    {{ $funcionario->cargo->nome ?? '-' }}
                                </td>

                                <td data-label="Última entrega">
                                    @if($ultimaEntrega)
                                        <strong>{{ $ultimaEntrega->data_entrega?->format('d/m/Y') }}</strong>
                                        <div class="text-muted-small">
                                            Entrega #{{ $ultimaEntrega->id }}
                                        </div>
                                    @else
                                        <span class="text-muted-small">Nenhuma entrega registrada</span>
                                    @endif
                                </td>

                                <td data-label="Itens da última entrega">
                                    @if($ultimaEntrega && $ultimaEntrega->itens->count())
                                        <div class="simple-list">
                                            @foreach($ultimaEntrega->itens as $item)
                                                <div class="simple-item">
                                                    <div class="simple-item__title">
                                                        {{ $item->produto->nome ?? '-' }}
                                                    </div>

                                                    @if($item->variacao)
                                                        <div class="text-muted-small">
                                                            {{ $item->variacao->nome_variacao }}
                                                            {{ $item->variacao->cor ?? '' }}
                                                            {{ $item->variacao->tamanho ?? '' }}
                                                            @if($item->variacao->sku)
                                                                | SKU: {{ $item->variacao->sku }}
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-muted-small">Sem variação</div>
                                                    @endif

                                                    <div class="text-muted-small">
                                                        Quantidade: {{ number_format((float) $item->quantidade, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted-small">Sem itens entregues</span>
                                    @endif
                                </td>

                                <td data-label="Último comprovante">
                                    @if($ultimaEntrega)
                                        @if($ultimaEntrega->status_comprovante === 'anexado')
                                            <span class="badge-status badge-success">Anexado</span>

                                            @if($ultimoComprovante)
                                                <div class="text-muted-small" style="margin-top:6px;">
                                                    Último arquivo enviado
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge-status badge-warning">Pendente</span>
                                        @endif
                                    @else
                                        <span class="badge-status badge-info">Sem entrega</span>
                                    @endif
                                </td>

                                <td data-label="Ações" style="text-align:right;">
                                    <div class="table-actions">
                                        <a href="{{ route('entregas.index', ['open_create' => 1, 'obra_id' => $funcionario->obra_id, 'funcionario_id' => $funcionario->id]) }}" class="btn btn-green">
                                            <i class="bi bi-plus-circle"></i>
                                            Entregar
                                        </a>

                                        <a href="{{ route('epi.historico', $funcionario->id) }}" class="btn btn-dark">
                                            <i class="bi bi-clock-history"></i>
                                            Histórico
                                        </a>

                                        @if($ultimaEntrega)
                                            <a href="{{ route('epi.pdf.ultima', $funcionario->id) }}" class="btn btn-dark">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                                Baixar lista EPI
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <div class="empty-state__title">Nenhum funcionário encontrado</div>
                                        <div class="text-muted-small">Tente ajustar os filtros ou cadastrar novos funcionários.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formFiltrosEpi = document.getElementById('formFiltrosEpi');
            const filtroBuscaFuncionarioEpi = document.getElementById('filtroBuscaFuncionarioEpi');
            const filtroCargoEpi = document.getElementById('filtroCargoEpi');
            const filtroObraEpi = document.getElementById('filtroObraEpi');
            const filtroComprovanteEpi = document.getElementById('filtroComprovanteEpi');

            let filtroTimeout = null;

            if (filtroBuscaFuncionarioEpi && formFiltrosEpi) {
                filtroBuscaFuncionarioEpi.addEventListener('input', function () {
                    clearTimeout(filtroTimeout);

                    filtroTimeout = setTimeout(function () {
                        formFiltrosEpi.submit();
                    }, 400);
                });
            }

            if (filtroCargoEpi && formFiltrosEpi) {
                filtroCargoEpi.addEventListener('change', function () {
                    formFiltrosEpi.submit();
                });
            }

            if (filtroObraEpi && formFiltrosEpi) {
                filtroObraEpi.addEventListener('change', function () {
                    formFiltrosEpi.submit();
                });
            }

            if (filtroComprovanteEpi && formFiltrosEpi) {
                filtroComprovanteEpi.addEventListener('change', function () {
                    formFiltrosEpi.submit();
                });
            }
        });
    </script>
@endsection