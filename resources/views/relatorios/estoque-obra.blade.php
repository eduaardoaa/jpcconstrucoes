@extends('layouts.app')

@section('title', 'Relatório - Estoque por obra')
@section('pageTitle', 'Relatório - Estoque por obra')
@section('pageDescription', 'Consulte saldo, consumo e cobertura estimada por obra.')

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
        padding: 18px 20px;
        background: transparent;
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

    .entity-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .entity-avatar {
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

    .entity-cell__name {
        font-weight: 600;
        color: var(--text-strong);
        line-height: 1.2;
    }

    .entity-cell__sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .metric-value {
        font-weight: 700;
        color: var(--text-strong);
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

        .form-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .card-header,
        .card-body {
            padding-left: 16px;
            padding-right: 16px;
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
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-building-check"></i></div>
            <div class="page-head__text">
                <h2>Relatório de estoque por obra</h2>
                <p>Consulte estoque atual, total entregue, média de consumo e dias estimados restantes.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('relatorios.estoque-obra.pdf', request()->only(['obra_id', 'produto_id', 'data_inicio', 'data_fim'])) }}" class="btn btn-dark">
                <i class="bi bi-file-earmark-pdf"></i>
                Baixar PDF
            </a>
            <a href="{{ route('relatorios.index') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom:18px;">
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.estoque-obra') }}">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-building"></i>
                            Obra
                        </label>
                        <select name="obra_id" class="form-control-custom">
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
                            <i class="bi bi-box-seam"></i>
                            Produto
                        </label>
                        <select name="produto_id" class="form-control-custom">
                            <option value="">Todos</option>
                            @foreach($produtos as $produto)
                                <option value="{{ $produto->id }}" {{ (string) $produtoId === (string) $produto->id ? 'selected' : '' }}>
                                    {{ $produto->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar-event"></i>
                            Data início
                        </label>
                        <input type="date" name="data_inicio" class="form-control-custom" value="{{ $dataInicio->format('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar-check"></i>
                            Data fim
                        </label>
                        <input type="date" name="data_fim" class="form-control-custom" value="{{ $dataFim->format('Y-m-d') }}">
                    </div>

                    <div class="form-group form-group-full">
                        <div class="actions-inline" style="justify-content:flex-end;">
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-funnel"></i>
                                Aplicar filtros
                            </button>
                            <a href="{{ route('relatorios.estoque-obra') }}" class="btn btn-dark">
                                <i class="bi bi-arrow-clockwise"></i>
                                Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Resultados ({{ $estoques->count() }})</div>
                <div class="card-subtitle">
                    Período analisado: {{ $dataInicio->format('d/m/Y') }} até {{ $dataFim->format('d/m/Y') }}.
                </div>
            </div>
        </div>

        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Obra</th>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>Estoque atual</th>
                            <th>Total entregue</th>
                            <th>Média diária</th>
                            <th>Dias restantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estoques as $item)
                            @php
                                $iniciaisObra = collect(explode(' ', trim($item['obra_nome'] ?? '')))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');

                                $iniciaisProduto = collect(explode(' ', trim($item['produto_nome'] ?? '')))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');
                            @endphp
                            <tr>
                                <td data-label="Obra">
                                    <div class="entity-cell">
                                        <span class="entity-avatar">{{ $iniciaisObra ?: 'OB' }}</span>
                                        <div>
                                            <div class="entity-cell__name">{{ $item['obra_nome'] }}</div>
                                            <div class="entity-cell__sub">Obra vinculada</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Produto">
                                    <div class="entity-cell">
                                        <span class="entity-avatar">{{ $iniciaisProduto ?: 'PR' }}</span>
                                        <div>
                                            <div class="entity-cell__name">{{ $item['produto_nome'] }}</div>
                                            <div class="entity-cell__sub">Produto analisado</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Variação">
                                    <div class="entity-cell__name">{{ $item['variacao_nome'] ?? '-' }}</div>
                                </td>

                                <td data-label="Estoque atual">
                                    <span class="metric-value">{{ number_format((float) $item['estoque_atual'], 0, ',', '.') }}</span>
                                </td>

                                <td data-label="Total entregue">
                                    <span class="metric-value">{{ number_format((float) $item['total_entregue'], 0, ',', '.') }}</span>
                                </td>

                                <td data-label="Média diária">
                                    <span class="metric-value">{{ number_format((float) $item['media_diaria'], 2, ',', '.') }}</span>
                                </td>

                                <td data-label="Dias restantes">
                                    @if($item['dias_restantes'] === null)
                                        <span class="badge-status badge-info">Sem consumo</span>
                                    @elseif($item['dias_restantes'] <= 7)
                                        <span class="badge-status badge-warning">{{ number_format($item['dias_restantes'], 1, ',', '.') }} dias</span>
                                    @else
                                        <span class="badge-status badge-success">{{ number_format($item['dias_restantes'], 1, ',', '.') }} dias</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-inboxes"></i>
                                        <div class="empty-state__title">Nenhum dado encontrado</div>
                                        <div>Não houve resultados para os filtros informados.</div>
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