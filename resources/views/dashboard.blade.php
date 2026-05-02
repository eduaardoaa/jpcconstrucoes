@extends('layouts.app')

@section('title', 'Dashboard')
@section('pageTitle', 'Dashboard')
@section('pageDescription', 'Visão geral operacional do sistema.')

@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    :root {
        --accent: #3b82f6;
        --accent-2: #2563eb;
        --accent-soft: rgba(59, 130, 246, 0.1);
        --accent-ring: rgba(59, 130, 246, 0.2);
        --neutral-1: #0b1220;
        --neutral-2: #0f172a;
        --border: rgba(148, 163, 184, 0.12);
        --border-strong: rgba(148, 163, 184, 0.2);
        --text: #e2e8f0;
        --text-strong: #f8fafc;
        --text-muted: #94a3b8;
        --text-subtle: #64748b;
        --success: #10b981;
        --success-soft: rgba(16, 185, 129, 0.1);
        --warning: #f59e0b;
        --warning-soft: rgba(245, 158, 11, 0.1);
        --danger: #ef4444;
        --danger-soft: rgba(239, 68, 68, 0.1);
        --radius-xl: 24px;
        --radius-lg: 18px;
        --radius-md: 14px;
        --font-main: 'Inter', sans-serif;
    }

    body { font-family: var(--font-main); }

    .page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        animation: fadeInDown 0.6s ease-out;
    }

    .page-head__left { display: flex; gap: 16px; align-items: center; }
    .page-head__icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.05));
        color: #60a5fa;
        font-size: 24px;
        border: 1px solid rgba(59, 130, 246, 0.2);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .page-head__text h2 {
        margin: 0;
        font-size: 22px;
        color: var(--text-strong);
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .page-head__text p {
        margin: 4px 0 0;
        font-size: 14px;
        color: var(--text-muted);
    }

    .card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    }

    .card:hover {
        border-color: var(--border-strong);
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.2);
    }

    .card-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        color: var(--text-strong);
        font-weight: 700;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-subtitle {
        color: var(--text-subtle);
        font-size: 12px;
        margin-top: 4px;
    }

    .card-body { padding: 24px; }

    .grid { display: grid; gap: 20px; }
    .grid-1 { grid-template-columns: 1fr; }
    .grid-2 { grid-template-columns: repeat(2, 1fr); }
    .grid-4 { grid-template-columns: repeat(4, 1fr); }

    .dashboard-kpi-card {
        position: relative;
        border: 1px solid rgba(255,255,255,0.05);
    }

    .dashboard-kpi-card .card-body {
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .dashboard-kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .dashboard-kpi-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-strong);
        letter-spacing: -0.03em;
    }

    .dashboard-kpi-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        border-radius: 12px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        font-family: var(--font-main);
    }

    .btn-green {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-green:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }

    .btn-dark {
        background: rgba(30, 41, 59, 0.5);
        border-color: var(--border);
        color: var(--text);
    }

    .btn-dark:hover {
        background: rgba(30, 41, 59, 0.8);
        border-color: var(--border-strong);
    }

    .form-control-custom {
        width: 100%;
        background: rgba(11, 18, 32, 0.4);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 14px;
        color: var(--text-strong);
        font-size: 14px;
        transition: all 0.2s;
        outline: none;
    }

    .form-control-custom:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px var(--accent-ring);
        background: rgba(11, 18, 32, 0.6);
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background: rgba(15, 23, 42, 0.4);
        padding: 14px 18px;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-muted);
        font-weight: 700;
        border-bottom: 1px solid var(--border);
    }

    .table tbody td {
        padding: 16px 18px;
        font-size: 14px;
        color: var(--text);
        border-bottom: 1px solid var(--border);
        transition: all 0.2s;
    }

    .table tbody tr:hover td {
        background: rgba(59, 130, 246, 0.03);
        color: var(--text-strong);
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success { background: var(--success-soft); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.2); }
    .badge-warning { background: var(--warning-soft); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.2); }
    .badge-info { background: var(--info-soft); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2); }

    .dashboard-alert-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }

    .dashboard-alert-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: var(--border-strong);
    }

    .metric-strong {
        font-weight: 700;
        color: var(--text-strong);
        font-size: 15px;
    }

    .rank-badge {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
    }
    .rank-1 { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3); }
    .rank-2 { background: #94a3b8; box-shadow: 0 2px 8px rgba(148, 163, 184, 0.3); }
    .rank-3 { background: #b47850; box-shadow: 0 2px 8px rgba(180, 120, 80, 0.3); }
    .rank-other { background: rgba(148, 163, 184, 0.1); color: var(--text-muted); }

    .db-empty-state {

        text-align: center;
        padding: 40px 20px;
        color: var(--text-subtle);
    }

    .db-empty-state i {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        opacity: 0.2;
    }

    .db-empty-state p {
        margin: 0;
        font-size: 14px;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }


    @media (max-width: 1024px) {
        .grid-4 { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 640px) {
        .grid-2, .grid-4 { grid-template-columns: 1fr; }
        .page-head { flex-direction: column; align-items: flex-start; }
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

    <div class="grid grid-4" style="margin-bottom:24px;">
        @if($podeVerFuncionarios)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6"><i class="bi bi-people-fill"></i></div>
                    <div class="dashboard-kpi-label">Funcionários ativos</div>
                    <div class="dashboard-kpi-value">{{ $totalFuncionarios ?? 0 }}</div>
                </div>
            </div>
        @endif

        @if($podeVerObras)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(168,85,247,0.1);color:#a855f7"><i class="bi bi-building-fill"></i></div>
                    <div class="dashboard-kpi-label">Obras ativas</div>
                    <div class="dashboard-kpi-value">{{ $totalObras ?? 0 }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(34,197,94,0.1);color:#22c55e"><i class="bi bi-box-seam-fill"></i></div>
                    <div class="dashboard-kpi-label">Entregas no período</div>
                    <div class="dashboard-kpi-value">{{ $totalEntregasPeriodo ?? 0 }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(56,189,248,0.1);color:#38bdf8"><i class="bi bi-layers-fill"></i></div>
                    <div class="dashboard-kpi-label">Itens entregues no período</div>
                    <div class="dashboard-kpi-value">{{ number_format($totalItensEntreguesPeriodo ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b"><i class="bi bi-calendar-check-fill"></i></div>
                    <div class="dashboard-kpi-label">Entregas no mês</div>
                    <div class="dashboard-kpi-value">{{ $totalEntregasMes ?? 0 }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(96,165,250,0.1);color:#60a5fa"><i class="bi bi-stack"></i></div>
                    <div class="dashboard-kpi-label">Itens entregues no mês</div>
                    <div class="dashboard-kpi-value">{{ number_format($totalItensMes ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEntregas)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(239,68,68,0.1);color:#ef4444"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div class="dashboard-kpi-label">Comprovantes pendentes</div>
                    <div class="dashboard-kpi-value">{{ $entregasPendentesComprovante ?? 0 }}</div>
                </div>
            </div>
        @endif

        @if($podeVerEstoque)
            <div class="card dashboard-kpi-card">
                <div class="card-body">
                    <div class="dashboard-kpi-icon" style="background:rgba(34,197,94,0.1);color:#22c55e"><i class="bi bi-archive-fill"></i></div>
                    <div class="dashboard-kpi-label">Estoque total atual</div>
                    <div class="dashboard-kpi-value">{{ number_format($estoqueTotal ?? 0, 0, ',', '.') }}</div>
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
                <div class="grid grid-4" style="margin-bottom:24px;">
                    <div class="dashboard-summary-box">
                        <div class="dashboard-kpi-label"><i class="bi bi-calendar3" style="margin-right:6px"></i>Período analisado</div>
                        <div style="font-size:15px; font-weight:700; margin-top:6px; color:var(--text-strong)">{{ $dataInicio->format('d/m/Y') }} até {{ $dataFim->format('d/m/Y') }}</div>
                    </div>

                    <div class="dashboard-summary-box">
                        <div class="dashboard-kpi-label"><i class="bi bi-hourglass-split" style="margin-right:6px"></i>Duração</div>
                        <div style="font-size:15px; font-weight:700; margin-top:6px; color:var(--text-strong)">{{ $diasPeriodo }} {{ $diasPeriodo == 1 ? 'dia' : 'dias' }}</div>
                    </div>

                    <div class="dashboard-summary-box">
                        <div class="dashboard-kpi-label"><i class="bi bi-trophy" style="margin-right:6px"></i>Maior consumo</div>
                        <div style="font-size:15px; font-weight:700; margin-top:6px; color:var(--text-strong); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $topObraConsumo->obra_nome ?? '-' }}</div>
                    </div>

                    <div class="dashboard-summary-box">
                        <div class="dashboard-kpi-label"><i class="bi bi-star-fill" style="margin-right:6px"></i>Mais entregue</div>
                        <div style="font-size:15px; font-weight:700; margin-top:6px; color:var(--text-strong); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $topProdutoConsumo['product_name'] ?? '-' }}</div>
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
                                    @forelse($consumoPorObra->take(12) as $index => $item)
                                        <tr>
                                            <td style="display:flex; align-items:center; gap:12px;">
                                                @if($index < 3)
                                                    <span class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                                @else
                                                    <span class="rank-badge rank-other">{{ $index + 1 }}</span>
                                                @endif
                                                {{ $item->obra_nome }}
                                            </td>
                                            <td style="font-weight:700; color:var(--text-strong);">{{ number_format((float) $item->total, 0, ',', '.') }}</td>
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
                                    @forelse($produtosMaisEntregues->take(12) as $index => $item)
                                        <tr>
                                            <td style="display:flex; align-items:center; gap:12px;">
                                                @if($index < 3)
                                                    <span class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                                @else
                                                    <span class="rank-badge rank-other">{{ $index + 1 }}</span>
                                                @endif
                                                {{ $item['product_name'] }}
                                            </td>
                                            <td>{{ $item['variant_name'] ?? '-' }}</td>
                                            <td style="font-weight:700; color:var(--text-strong);">{{ number_format((float) $item['total'], 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <div class="db-empty-state">
                                                    <i class="bi bi-cart-x"></i>
                                                    <p>Sem produtos entregues no período.</p>
                                                </div>
                                            </td>
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
                                        <td colspan="3">
                                            <div class="db-empty-state">
                                                <i class="bi bi-box-seam"></i>
                                                <p>Nenhum item zerado no momento.</p>
                                            </div>
                                        </td>
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
                                                <span class="badge-status badge-warning">{{ number_format($item['coverage_days'], 0) }} dias</span>
                                            @else
                                                <span class="badge-status badge-success">{{ number_format($item['coverage_days'], 0) }} dias</span>
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
                            <div class="db-empty-state" style="padding: 20px;">
                                <i class="bi bi-check-circle" style="color:var(--success);opacity:0.6"></i>
                                <p>Nenhuma pendência crítica no momento.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($podeVerEntregas)
        <div class="grid grid-2" style="margin-bottom:18px;">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title"><i class="bi bi-graph-up" style="color:#3b82f6;margin-right:6px"></i>Entregas por dia (últimos 7 dias)</div>
                        <div class="card-subtitle">Quantidade de lotes registrados por dia.</div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:220px"><canvas id="dashChartOrders"></canvas></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title"><i class="bi bi-bar-chart-fill" style="color:#60a5fa;margin-right:6px"></i>Itens entregues por dia (últimos 7 dias)</div>
                        <div class="card-subtitle">Volume total de itens movimentados por entrega.</div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:220px"><canvas id="dashChartItems"></canvas></div>
                </div>
            </div>
        </div>
    @endif

    <script>
    function dashInitCharts() {
        if (typeof Chart === 'undefined') {
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js';
            s.onload = function() { dashBuildCharts(); };
            document.head.appendChild(s);
        } else {
            dashBuildCharts();
        }
    }

    function dashBuildCharts() {
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = 'rgba(148,163,184,0.06)';
        Chart.defaults.font.family = "'Inter', sans-serif";

        @if($podeVerEntregas)
        var ordersCtx = document.getElementById('dashChartOrders');
        if (ordersCtx) {
            var ordersData = @json($ordersByDay->values());
            var gradient = ordersCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

            new Chart(ordersCtx, {
                type: 'bar',
                data: {
                    labels: ordersData.map(function(d) { return d.label; }),
                    datasets: [{
                        label: 'Entregas',
                        data: ordersData.map(function(d) { return d.value; }),
                        backgroundColor: gradient,
                        borderRadius: 10,
                        borderSkipped: false,
                        barThickness: 28
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#f8fafc',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, min: 0, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.06)' } },
                        x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                    }
                }
            });
        }

        var itemsCtx = document.getElementById('dashChartItems');
        if (itemsCtx) {
            var itemsData = @json($itemsOutByDay->values());
            var lineGradient = itemsCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
            lineGradient.addColorStop(0, 'rgba(96, 165, 250, 0.2)');
            lineGradient.addColorStop(1, 'rgba(96, 165, 250, 0)');

            new Chart(itemsCtx, {
                type: 'line',
                data: {
                    labels: itemsData.map(function(d) { return d.label; }),
                    datasets: [{
                        label: 'Itens',
                        data: itemsData.map(function(d) { return d.value; }),
                        borderColor: '#60a5fa',
                        backgroundColor: lineGradient,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#60a5fa',
                        pointBorderColor: '#0f172a',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#f8fafc',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, min: 0, ticks: { font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.06)' } },
                        x: { ticks: { font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.03)' } }
                    }
                }
            });
        }
        @endif
    }


    dashInitCharts();
    </script>
@endsection