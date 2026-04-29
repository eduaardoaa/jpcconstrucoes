@extends('layouts.app')

@section('title', 'Dashboard')
@section('pageTitle', 'Dashboard')
@section('pageDescription', 'Visão geral operacional do sistema.')

@section('content')
    <style>
    :root {
        --accent: #0d6efd;
        --accent-2: #0b5ed7;
        --accent-soft: rgba(13, 110, 253, 0.10);
        --accent-ring: rgba(13, 110, 253, 0.18);

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
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.22), rgba(13, 110, 253, 0.08));
        color: #6ea8fe;
        font-size: 20px;
        flex-shrink: 0;
        border: 1px solid rgba(13, 110, 253, 0.18);
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

    .grid {
        display: grid;
        gap: 16px;
    }

    .grid-1 { grid-template-columns: 1fr; }
    .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

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
        background: linear-gradient(135deg, #0b5ed7, #0d6efd);
        color: #fff;
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.22);
    }

    .btn-green:hover {
        box-shadow: 0 10px 24px rgba(13, 110, 253, 0.32);
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
        align-items: flex-start;
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
        min-width: 0;
    }

    .card-header {
        padding: 18px 20px 14px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.015);
        flex-wrap: wrap;
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

    .card-body {
        padding: 20px;
        background: transparent;
    }

    .dashboard-highlight {
        border-color: rgba(13, 110, 253, 0.28);
        box-shadow: 0 0 0 1px rgba(13, 110, 253, 0.06), 0 18px 40px rgba(0, 0, 0, 0.18);
    }

    .dashboard-kpi-card .card-body {
        padding: 18px;
    }

    .dashboard-kpi-value {
        font-size: 2rem;
        font-weight: 800;
        margin-top: 8px;
        line-height: 1.1;
        word-break: break-word;
        color: var(--text-strong);
    }

    .text-muted-small {
        font-size: 12px;
        color: var(--text-muted);
    }

    .dashboard-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .dashboard-summary-box {
        padding: 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        min-width: 0;
    }

    .dashboard-summary-box strong {
        display: block;
        font-size: 1.1rem;
        margin-top: 6px;
        line-height: 1.25;
        word-break: break-word;
        color: var(--text-strong);
    }

    .dashboard-alert-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .dashboard-alert-item {
        padding: 12px 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        overflow: hidden;
    }

    .metric-strong {
        font-weight: 700;
        color: var(--text-strong);
    }

    .table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        border-radius: 14px;
    }

    .table-wrap::-webkit-scrollbar {
        height: 8px;
    }

    .table-wrap::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.14);
        border-radius: 999px;
    }

    .table {
        width: 100%;
        min-width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background: transparent;
    }

    .table th,
    .table td {
        vertical-align: middle;
        white-space: nowrap;
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
        background: transparent;
        transition: background-color var(--t-med) var(--ease);
    }

    .table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.025);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table-mobile-sm { min-width: 520px; }
    .table-mobile-md { min-width: 680px; }
    .table-mobile-lg { min-width: 820px; }

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

    .mini-bars {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        min-height: 220px;
        padding-top: 14px;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 6px;
    }

    .mini-bar-item {
        flex: 1;
        min-width: 42px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .mini-bar {
        width: 100%;
        max-width: 52px;
        border-radius: 14px 14px 8px 8px;
        background: linear-gradient(180deg, rgba(13, 110, 253, .95), rgba(13, 110, 253, .45));
        min-height: 10px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        color: #fff;
        font-size: .76rem;
        font-weight: 700;
        padding-top: 6px;
    }

    .mini-bar.secondary {
        background: linear-gradient(180deg, rgba(59, 130, 246, .95), rgba(59, 130, 246, .45));
    }

    .mini-bar-label {
        font-size: .8rem;
        color: var(--text-muted);
        text-align: center;
        white-space: nowrap;
    }

    .mini-bar-value {
        font-size: .85rem;
        font-weight: 700;
        text-align: center;
        color: var(--text-strong);
    }

    @media (max-width: 1100px) {
        .grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 820px) {
        .page-head {
            flex-direction: column;
            align-items: stretch;
        }

        .page-head__left {
            width: 100%;
        }

        .actions-inline {
            width: 100%;
        }

        .actions-inline .btn {
            width: 100%;
        }

        .grid-2,
        .grid-4,
        .dashboard-summary-grid {
            grid-template-columns: 1fr;
        }

        .card-header {
            padding: 14px 16px;
        }

        .card-body {
            padding: 16px;
        }
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-speedometer2"></i></div>
            <div class="page-head__text">
                <h2>Dashboard Operacional</h2>
                <p>Acompanhe consumo, cobertura de estoque, pendências de comprovante e movimentação geral do sistema.</p>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:18px;">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}">
                <div class="grid grid-4">
                    <div>
                        <label class="text-muted-small" style="display:block; margin-bottom:8px;">Data início</label>
                        <input type="date" name="data_inicio" class="form-control-custom" value="{{ $dataInicio->format('Y-m-d') }}">
                    </div>

                    <div>
                        <label class="text-muted-small" style="display:block; margin-bottom:8px;">Data fim</label>
                        <input type="date" name="data_fim" class="form-control-custom" value="{{ $dataFim->format('Y-m-d') }}">
                    </div>

                    <div style="grid-column: span 2;">
                        <label class="text-muted-small" style="display:block; margin-bottom:8px;">Ações</label>
                        <div class="actions-inline">
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-funnel"></i>
                                Aplicar
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-dark">
                                <i class="bi bi-arrow-clockwise"></i>
                                Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($criticalAlerts->count())
        <div class="alert-error-box" style="margin-bottom:18px;">
            <div>
                <strong>Alertas críticos:</strong>
                <ul style="margin:8px 0 0 18px; padding:0;">
                    @foreach($criticalAlerts as $alert)
                        <li>{{ $alert }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-4" style="margin-bottom:18px;">
        @if($podeVerFuncionarios)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Funcionários ativos</div>
                    <div class="dashboard-kpi-value">{{ $totalFuncionarios }}</div>
                </div>
            </div>
        @endif

        @if($podeVerObras)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Obras ativas</div>
                    <div class="dashboard-kpi-value">{{ $totalObras }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Entregas no período</div>
                    <div class="dashboard-kpi-value">{{ $totalEntregasPeriodo }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Itens entregues no período</div>
                    <div class="dashboard-kpi-value">{{ number_format($totalItensEntreguesPeriodo, 0, ',', '.') }}</div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-4" style="margin-bottom:18px;">
        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Entregas no mês</div>
                    <div class="dashboard-kpi-value">{{ $totalEntregasMes }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Itens entregues no mês</div>
                    <div class="dashboard-kpi-value">{{ number_format($totalItensMes, 0, ',', '.') }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Comprovantes pendentes</div>
                    <div class="dashboard-kpi-value">{{ $entregasPendentesComprovante }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEstoque)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="text-muted-small">Estoque total atual</div>
                    <div class="dashboard-kpi-value">{{ number_format($estoqueTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-1" style="margin-bottom:18px;">
        <div class="card dashboard-highlight">
            <div class="card-header">
                <div>
                    <div class="card-title">Resumo executivo do período</div>
                    <div class="card-subtitle">Os dados abaixo ajudam a ver rapidamente quem mais consome e onde está o maior risco.</div>
                </div>
            </div>

            <div class="card-body">
                <div class="dashboard-summary-grid">
                    <div class="dashboard-summary-box">
                        <div class="text-muted-small">Período analisado</div>
                        <strong>{{ $dataInicio->format('d/m/Y') }} até {{ $dataFim->format('d/m/Y') }}</strong>
                    </div>

                    <div class="dashboard-summary-box">
                        <div class="text-muted-small">Duração do período</div>
                        <strong>{{ $diasPeriodo }} dia(s)</strong>
                    </div>

                    <div class="dashboard-summary-box">
                        <div class="text-muted-small">Obra com maior consumo</div>
                        <strong>{{ $topObraConsumo->obra_nome ?? '-' }}</strong>
                    </div>

                    <div class="dashboard-summary-box">
                        <div class="text-muted-small">Produto mais entregue</div>
                        <strong>{{ $topProdutoConsumo['product_name'] ?? '-' }}</strong>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="table table-mobile-md">
                        <thead>
                            <tr>
                                <th>Indicador</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($podeVerProdutos)
                                <tr>
                                    <td>Total de produtos ativos</td>
                                    <td>{{ $totalProdutos }}</td>
                                </tr>
                            @endif
                            @if($podeVerEstoque)
                                <tr>
                                    <td>Itens zerados</td>
                                    <td>{{ $zeroStockItems->count() }}</td>
                                </tr>
                                <tr>
                                    <td>Itens com estoque crítico</td>
                                    <td>{{ $criticalStockItems->count() }}</td>
                                </tr>
                            @endif
                            @if($podeVerObras)
                                <tr>
                                    <td>Top obra no período</td>
                                    <td>
                                        {{ $topObraConsumo->obra_nome ?? '-' }}
                                        @if($topObraConsumo)
                                            - {{ number_format((float) $topObraConsumo->total, 0, ',', '.') }} item(ns)
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if($podeVerProdutos)
                                <tr>
                                    <td>Top produto no período</td>
                                    <td>
                                        @if($topProdutoConsumo)
                                            {{ $topProdutoConsumo['product_name'] }}
                                            @if($topProdutoConsumo['variant_name'])
                                                / {{ $topProdutoConsumo['variant_name'] }}
                                            @endif
                                            - {{ number_format((float) $topProdutoConsumo['total'], 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($podeVerObras || $podeVerProdutos)
        <div class="grid grid-2" style="margin-bottom:18px;">
            @if($podeVerObras)
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Top obras com maior consumo</div>
                            <div class="card-subtitle">Quantidade total entregue no período selecionado.</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-wrap">
                            <table class="table table-mobile-sm">
                                <thead>
                                    <tr>
                                        <th>Obra</th>
                                        <th>Total entregue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($consumoPorObra->take(12) as $item)
                                        <tr>
                                            <td>{{ $item->obra_nome }}</td>
                                            <td>{{ number_format((float) $item->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">Sem consumo no período.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($podeVerProdutos)
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Top produtos entregues</div>
                            <div class="card-subtitle">Produtos e variações com maior saída no período.</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-wrap">
                            <table class="table table-mobile-md">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Variação</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($produtosMaisEntregues->take(12) as $item)
                                        <tr>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td>{{ $item['variant_name'] ?? '-' }}</td>
                                            <td>{{ number_format((float) $item['total'], 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">Sem produtos entregues no período.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($podeVerFuncionarios || $podeVerEntregas)
        <div class="grid grid-2" style="margin-bottom:18px;">
            @if($podeVerFuncionarios)
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Funcionários com mais retiradas</div>
                            <div class="card-subtitle">Somatório de itens recebidos no período.</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-wrap">
                            <table class="table table-mobile-sm">
                                <thead>
                                    <tr>
                                        <th>Funcionário</th>
                                        <th>Total retirado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($funcionariosMaisRetiradas as $item)
                                        <tr>
                                            <td>{{ $item->funcionario_nome }}</td>
                                            <td>{{ number_format((float) $item->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">Sem retiradas no período.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($podeVerEntregas)
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Entregas com comprovante pendente</div>
                            <div class="card-subtitle">As mais recentes que ainda precisam de anexo.</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-wrap">
                            <table class="table table-mobile-lg">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Funcionário</th>
                                        <th>Obra</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entregasPendentesLista as $item)
                                        <tr>
                                            <td>{{ $item->data_entrega?->format('d/m/Y') }}</td>
                                            <td>{{ $item->funcionario->nome ?? '-' }}</td>
                                            <td>{{ $item->obra->nome ?? '-' }}</td>
                                            <td>
                                                <span class="badge-status badge-warning">Pendente</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Nenhuma entrega pendente de comprovante.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if($podeVerEstoque)
        <div class="grid grid-2" style="margin-bottom:18px;">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Itens zerados</div>
                        <div class="card-subtitle">Produtos ou variações sem estoque no momento.</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrap">
                        <table class="table table-mobile-md">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Variação</th>
                                    <th>Obra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($zeroStockItems->take(10) as $item)
                                    <tr>
                                        <td>{{ $item['product_name'] }}</td>
                                        <td>{{ $item['variant_name'] ?? '-' }}</td>
                                        <td>{{ $item['obra_name'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">Nenhum item zerado no momento.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Estoque crítico</div>
                        <div class="card-subtitle">Itens com estoque entre 1 e 10 unidades.</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrap">
                        <table class="table table-mobile-md">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Variação</th>
                                    <th>Obra</th>
                                    <th>Estoque</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($criticalStockItems->take(10) as $item)
                                    <tr>
                                        <td>{{ $item['product_name'] }}</td>
                                        <td>{{ $item['variant_name'] ?? '-' }}</td>
                                        <td>{{ $item['obra_name'] }}</td>
                                        <td>
                                            <span class="badge-status badge-warning">{{ $item['stock'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">Nenhum item em estoque crítico.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-2" style="margin-bottom:18px;">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Previsão de duração do estoque</div>
                        <div class="card-subtitle">Com base no consumo médio dos últimos 7 dias.</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrap">
                        <table class="table table-mobile-lg">
                            <thead>
                                <tr>
                                    <th>Obra</th>
                                    <th>Produto</th>
                                    <th>Variação</th>
                                    <th>Média/dia</th>
                                    <th>Dura aprox.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockCoverage->take(12) as $item)
                                    <tr>
                                        <td>{{ $item['obra_name'] }}</td>
                                        <td>{{ $item['product_name'] }}</td>
                                        <td>{{ $item['variant_name'] ?? '-' }}</td>
                                        <td>{{ number_format((float) $item['avg_daily'], 2, ',', '.') }}</td>
                                        <td>
                                            @if($item['coverage_days'] === null)
                                                <span class="badge-status badge-info">Sem consumo recente</span>
                                            @elseif($item['coverage_days'] <= 7)
                                                <span class="badge-status badge-warning">{{ $item['coverage_days'] }} dias</span>
                                            @else
                                                <span class="badge-status badge-success">{{ $item['coverage_days'] }} dias</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Ainda não há consumo suficiente para calcular previsão.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Pendências que exigem atenção</div>
                        <div class="card-subtitle">Use isso para atacar rápido os principais gargalos.</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dashboard-alert-list">
                        @if($entregasPendentesComprovante > 0)
                            <div class="dashboard-alert-item">
                                <div class="metric-strong">Comprovantes pendentes</div>
                                <div class="text-muted-small" style="margin-top:6px;">
                                    Existem <strong>{{ $entregasPendentesComprovante }}</strong> entrega(s) sem comprovante anexado.
                                </div>
                            </div>
                        @endif

                        @if($zeroStockItems->count() > 0)
                            <div class="dashboard-alert-item">
                                <div class="metric-strong">Itens zerados</div>
                                <div class="text-muted-small" style="margin-top:6px;">
                                    Existem <strong>{{ $zeroStockItems->count() }}</strong> item(ns) sem nenhuma unidade em estoque.
                                </div>
                            </div>
                        @endif

                        @if($criticalStockItems->count() > 0)
                            <div class="dashboard-alert-item">
                                <div class="metric-strong">Estoque crítico</div>
                                <div class="text-muted-small" style="margin-top:6px;">
                                    Existem <strong>{{ $criticalStockItems->count() }}</strong> item(ns) com estoque de até 10 unidades.
                                </div>
                            </div>
                        @endif

                        @if(
                            $entregasPendentesComprovante === 0
                            && $zeroStockItems->count() === 0
                            && $criticalStockItems->count() === 0
                        )
                            <div class="text-muted-small">Nenhuma pendência crítica encontrada no momento.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($podeVerEntregas)
        <div class="grid grid-2">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Entregas por dia (últimos 7 dias)</div>
                        <div class="card-subtitle">Quantidade de lotes registrados por dia.</div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $maxOrdersDay = max(1, $ordersByDay->max('value'));
                    @endphp

                    <div class="mini-bars">
                        @foreach($ordersByDay as $day)
                            @php
                                $height = max(12, ($day['value'] / $maxOrdersDay) * 180);
                            @endphp
                            <div class="mini-bar-item">
                                <div class="mini-bar-value">{{ $day['value'] }}</div>
                                <div class="mini-bar" style="height: {{ $height }}px;"></div>
                                <div class="mini-bar-label">{{ $day['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Itens entregues por dia (últimos 7 dias)</div>
                        <div class="card-subtitle">Volume total de itens movimentados por entrega.</div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $maxItemsDay = max(1, $itemsOutByDay->max('value'));
                    @endphp

                    <div class="mini-bars">
                        @foreach($itemsOutByDay as $day)
                            @php
                                $height = max(12, ($day['value'] / $maxItemsDay) * 180);
                            @endphp
                            <div class="mini-bar-item">
                                <div class="mini-bar-value">{{ $day['value'] }}</div>
                                <div class="mini-bar secondary" style="height: {{ $height }}px;"></div>
                                <div class="mini-bar-label">{{ $day['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection