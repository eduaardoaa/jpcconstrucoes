@extends('layouts.app')

@section('title', 'Relatório - Consumo por produto')
@section('pageTitle', 'Relatório - Consumo por produto')
@section('pageDescription', 'Veja o ranking de produtos e variações mais entregues.')

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

    .btn-dark {
        background: rgba(15, 23, 42, 0.72);
        border-color: var(--border-strong);
        color: var(--text);
    }

    .btn-dark:hover {
        background: rgba(30, 41, 59, 0.92);
        border-color: rgba(148, 163, 184, 0.34);
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
        padding: 0;
        background: transparent;
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
        vertical-align: middle;
        background: transparent;
        transition: background-color var(--t-med) var(--ease);
    }

    .table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.025);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .ranking-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ranking-badge {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        color: #6ea8fe;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.22), rgba(13, 110, 253, 0.08));
        border: 1px solid rgba(13, 110, 253, 0.18);
        flex-shrink: 0;
    }

    .ranking-title {
        font-weight: 700;
        color: var(--text-strong);
        line-height: 1.2;
    }

    .ranking-subtitle {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 3px;
    }

    .variation-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.08);
        border: 1px solid var(--border-soft);
        color: var(--text);
        font-size: 12.5px;
        font-weight: 600;
    }

    .variation-pill i {
        color: var(--text-muted);
        font-size: 12px;
    }

    .total-box {
        display: inline-flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }

    .total-value {
        font-size: 20px;
        font-weight: 800;
        line-height: 1;
        color: var(--text-strong);
        letter-spacing: -0.02em;
    }

    .total-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
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
            padding: 8px 0 !important;
            text-align: left !important;
            background: transparent !important;
        }

        .table td::before {
            content: attr(data-label);
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .total-box {
            align-items: flex-start;
        }
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-bar-chart-steps"></i></div>
            <div class="page-head__text">
                <h2>Consumo por produto</h2>
                <p>Ranking dos produtos e variações mais entregues no sistema.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('relatorios.consumo.pdf') }}" class="btn btn-dark">
                <i class="bi bi-file-earmark-pdf"></i>
                Baixar PDF
            </a>

            <a href="{{ route('relatorios.index') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Resultados ({{ $dados->count() }})</div>
                <div class="card-subtitle">Produtos e variações agrupados por quantidade total entregue.</div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>Total consumido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dados as $index => $item)
                            <tr>
                                <td data-label="Produto">
                                    <div class="ranking-cell">
                                        <span class="ranking-badge">#{{ $index + 1 }}</span>
                                        <div>
                                            <div class="ranking-title">{{ $item['produto'] }}</div>
                                            <div class="ranking-subtitle">Posição no ranking de consumo</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Variação">
                                    @if(!empty($item['variacao']) && $item['variacao'] !== '-')
                                        <span class="variation-pill">
                                            <i class="bi bi-diagram-3"></i>
                                            {{ $item['variacao'] }}
                                        </span>
                                    @else
                                        <span class="variation-pill">
                                            <i class="bi bi-dash-circle"></i>
                                            Sem variação
                                        </span>
                                    @endif
                                </td>

                                <td data-label="Total consumido">
                                    <div class="total-box">
                                        <div class="total-value">{{ number_format((float) $item['total'], 0, ',', '.') }}</div>
                                        <div class="total-label">itens entregues</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <i class="bi bi-box-seam"></i>
                                        <div class="empty-state__title">Nenhum consumo registrado</div>
                                        <div>Não há movimentações suficientes para exibir o ranking.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection