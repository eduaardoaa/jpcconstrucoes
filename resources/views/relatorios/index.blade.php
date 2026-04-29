@extends('layouts.app')

@section('title', 'Relatórios')
@section('pageTitle', 'Relatórios')
@section('pageDescription', 'Análises e exportações do sistema.')

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

    .reports-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .reports-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
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

    .card-body {
        padding: 18px 20px;
        background: transparent;
    }

    .metric-card {
        position: relative;
        overflow: hidden;
    }

    .metric-card::after {
        content: "";
        position: absolute;
        inset: auto -20% -45% auto;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(13, 110, 253, 0.12), transparent 70%);
        pointer-events: none;
    }

    .metric-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 12.5px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .metric-label i {
        color: #6ea8fe;
        font-size: 14px;
    }

    .metric-value {
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
        color: var(--text-strong);
        letter-spacing: -0.03em;
    }

    .metric-hint {
        margin-top: 8px;
        font-size: 12px;
        color: var(--text-subtle);
    }

    .report-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .report-card .card-body {
        display: flex;
        flex-direction: column;
        gap: 14px;
        align-items: flex-start;
    }

    .report-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.20), rgba(13, 110, 253, 0.08));
        color: #6ea8fe;
        border: 1px solid rgba(13, 110, 253, 0.18);
        font-size: 18px;
    }

    .report-description {
        color: var(--text-muted);
        font-size: 13px;
        line-height: 1.55;
        min-height: 40px;
    }

    @media (max-width: 1100px) {
        .reports-grid-4 {
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

        .reports-grid-4,
        .reports-grid-2 {
            grid-template-columns: 1fr;
        }

        .card-header,
        .card-body {
            padding-left: 16px;
            padding-right: 16px;
        }

        .report-card .btn {
            width: 100%;
        }
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-bar-chart-line-fill"></i></div>
            <div class="page-head__text">
                <h2>Relatórios</h2>
                <p>Visualize dados estratégicos e gere relatórios em PDF.</p>
            </div>
        </div>
    </div>

    <div class="reports-grid-4">
        <div class="card metric-card">
            <div class="card-body">
                <div class="metric-label">
                    <i class="bi bi-box-arrow-in-down"></i>
                    Total de entregas
                </div>
                <div class="metric-value">{{ $totalEntregas }}</div>
                <div class="metric-hint">Entregas registradas no sistema.</div>
            </div>
        </div>

        <div class="card metric-card">
            <div class="card-body">
                <div class="metric-label">
                    <i class="bi bi-people-fill"></i>
                    Funcionários ativos
                </div>
                <div class="metric-value">{{ $totalFuncionariosAtivos }}</div>
                <div class="metric-hint">Colaboradores atualmente ativos.</div>
            </div>
        </div>

        <div class="card metric-card">
            <div class="card-body">
                <div class="metric-label">
                    <i class="bi bi-exclamation-circle"></i>
                    Comprovantes pendentes
                </div>
                <div class="metric-value">{{ $totalComprovantesPendentes }}</div>
                <div class="metric-hint">Entregas ainda sem anexo enviado.</div>
            </div>
        </div>

        <div class="card metric-card">
            <div class="card-body">
                <div class="metric-label">
                    <i class="bi bi-calendar-check"></i>
                    Itens entregues no mês
                </div>
                <div class="metric-value">{{ $itensEntreguesMes }}</div>
                <div class="metric-hint">Movimentação do período atual.</div>
            </div>
        </div>
    </div>

    <div class="reports-grid-2">
        <div class="card report-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Estoque por obra</div>
                    <div class="card-subtitle">Saldo atual, consumo e cobertura estimada.</div>
                </div>
            </div>
            <div class="card-body">
                <div class="report-icon"><i class="bi bi-building"></i></div>
                <div class="report-description">
                    Consulte o estoque distribuído entre as obras e acompanhe a posição atual de materiais por unidade.
                </div>
                <a href="{{ route('relatorios.estoque-obra') }}" class="btn btn-green">
                    <i class="bi bi-box-arrow-up-right"></i>
                    Abrir relatório
                </a>
            </div>
        </div>

        <div class="card report-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Entregas por funcionário</div>
                    <div class="card-subtitle">Dados completos, última entrega e total de itens.</div>
                </div>
            </div>
            <div class="card-body">
                <div class="report-icon"><i class="bi bi-person-lines-fill"></i></div>
                <div class="report-description">
                    Analise o histórico individual de entregas, volumes distribuídos e a última movimentação de cada funcionário.
                </div>
                <a href="{{ route('relatorios.funcionarios') }}" class="btn btn-green">
                    <i class="bi bi-box-arrow-up-right"></i>
                    Abrir relatório
                </a>
            </div>
        </div>

        <div class="card report-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Consumo por produto</div>
                    <div class="card-subtitle">Ranking de produtos e variações mais entregues.</div>
                </div>
            </div>
            <div class="card-body">
                <div class="report-icon"><i class="bi bi-box-seam"></i></div>
                <div class="report-description">
                    Veja os produtos com maior saída, compare variações e identifique os itens mais consumidos no período.
                </div>
                <a href="{{ route('relatorios.consumo') }}" class="btn btn-green">
                    <i class="bi bi-box-arrow-up-right"></i>
                    Abrir relatório
                </a>
            </div>
        </div>

        <div class="card report-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Comprovantes pendentes</div>
                    <div class="card-subtitle">Controle das entregas que ainda precisam de anexo.</div>
                </div>
            </div>
            <div class="card-body">
                <div class="report-icon"><i class="bi bi-file-earmark-excel"></i></div>
                <div class="report-description">
                    Acompanhe rapidamente quais entregas ainda estão sem comprovante anexado e precisam de regularização.
                </div>
                <a href="{{ route('relatorios.comprovantes') }}" class="btn btn-green">
                    <i class="bi bi-box-arrow-up-right"></i>
                    Abrir relatório
                </a>
            </div>
        </div>
    </div>
@endsection