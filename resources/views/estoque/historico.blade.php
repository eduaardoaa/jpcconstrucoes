@extends('layouts.app')

@section('title', 'Histórico de Estoque')
@section('pageTitle', 'Histórico de Estoque')
@section('pageDescription', 'Visualize todas as movimentações realizadas no estoque.')

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

    input[type="date"].form-control-custom {
        color-scheme: dark;
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

    .text-muted-small {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 3px;
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

    .history-cell__name {
        font-weight: 600;
        color: var(--text-strong);
        line-height: 1.2;
    }

    .history-cell__sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .number-strong {
        font-weight: 700;
        color: var(--text-strong);
        font-variant-numeric: tabular-nums;
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

    .estoque-pagination-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 18px 20px 22px;
        border-top: 1px solid var(--border);
    }

    .estoque-pagination-wrap nav {
        width: 100%;
    }

    .estoque-pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between > div:first-child {
        display: none !important;
    }

    .estoque-pagination-wrap .d-none.flex-sm-fill.d-sm-flex.align-items-sm-center.justify-content-sm-between {
        justify-content: center !important;
    }

    .estoque-pagination-wrap .d-flex.justify-content-between.flex-fill.d-sm-none {
        display: none !important;
    }

    .estoque-pagination-wrap .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .estoque-pagination-wrap .page-item {
        list-style: none;
    }

    .estoque-pagination-wrap .pagination .page-item:first-child,
    .estoque-pagination-wrap .pagination .page-item:last-child {
        display: none !important;
    }

    .estoque-pagination-wrap .page-link,
    .estoque-pagination-wrap .page-item > span {
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,.08);
        background: rgba(255,255,255,.03);
        color: #e5e7eb;
        text-decoration: none;
        font-weight: 600;
        line-height: 1;
        font-size: .95rem;
        transition: .2s ease;
    }

    .estoque-pagination-wrap .page-link:hover {
        background: rgba(13, 110, 253, .14);
        border-color: rgba(13, 110, 253, .35);
        color: #fff;
        transform: translateY(-1px);
    }

    .estoque-pagination-wrap .page-item.active .page-link,
    .estoque-pagination-wrap .page-item.active > span {
        background: linear-gradient(135deg, rgba(13,110,253,.95), rgba(13,110,253,.72));
        border-color: rgba(13,110,253,.65);
        color: #fff;
    }

    .estoque-pagination-wrap .page-item.disabled .page-link,
    .estoque-pagination-wrap .page-item.disabled > span {
        opacity: .45;
        cursor: not-allowed;
        pointer-events: none;
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

        .estoque-pagination-wrap {
            padding: 12px 14px 18px;
        }
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-clock-history"></i></div>
            <div class="page-head__text">
                <h2>Histórico de movimentações</h2>
                <p>Consulte entradas e ajustes realizados no estoque.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('estoque.index') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left"></i>
                Voltar para estoque
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <div>
                <div class="card-title">Filtros do histórico</div>
                <div class="card-subtitle">Refine a visualização das movimentações realizadas.</div>
            </div>
        </div>

        <div class="filters-wrap" style="border-bottom:none;">
            <form method="GET" action="{{ route('estoque.historico') }}">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-building"></i>
                            Obra
                        </label>
                        <select name="obra_id" class="form-control-custom">
                            <option value="">Todas</option>
                            @foreach ($obras as $obra)
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
                            @foreach ($produtos as $produto)
                                <option value="{{ $produto->id }}" {{ (string) $produtoId === (string) $produto->id ? 'selected' : '' }}>
                                    {{ $produto->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-arrow-left-right"></i>
                            Tipo
                        </label>
                        <select name="tipo_movimentacao" class="form-control-custom">
                            <option value="">Todos</option>
                            <option value="entrada" {{ $tipo === 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="ajuste" {{ $tipo === 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar3"></i>
                            Data inicial
                        </label>
                        <input type="date" name="data_inicial" class="form-control-custom" value="{{ $dataInicial }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar3"></i>
                            Data final
                        </label>
                        <input type="date" name="data_final" class="form-control-custom" value="{{ $dataFinal }}">
                    </div>

                    <div class="form-group form-group-full">
                        <div class="actions-inline" style="justify-content:flex-end;">
                            <a href="{{ route('estoque.historico') }}" class="btn btn-dark">
                                <i class="bi bi-x-circle"></i>
                                Limpar filtros
                            </a>

                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-funnel"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Movimentações registradas</div>
                <div class="card-subtitle">Lista completa das alterações realizadas no estoque.</div>
            </div>

            <span class="card-count">
                {{ $movimentacoes->total() }} {{ $movimentacoes->total() === 1 ? 'movimentação' : 'movimentações' }}
            </span>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Obra</th>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>Tipo</th>
                            <th>Qtd</th>
                            <th>Antes</th>
                            <th>Depois</th>
                            <th>Usuário</th>
                        </tr>
                    </thead>
                    <tbody>
                                                @forelse ($movimentacoes as $movimentacao)
                            <tr>
                                <td data-label="Data">
                                    <div class="history-cell__name">
                                        {{ $movimentacao->data_movimentacao ? $movimentacao->data_movimentacao->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                <td data-label="Obra">
                                    <div class="history-cell__name">{{ $movimentacao->obra->nome ?? '-' }}</div>
                                </td>

                                <td data-label="Produto">
                                    <div class="history-cell__name">{{ $movimentacao->produto->nome ?? '-' }}</div>
                                </td>

                                <td data-label="Variação">
                                    @if ($movimentacao->variacao)
                                        <div class="history-cell__name">{{ $movimentacao->variacao->nome_variacao }}</div>
                                        <div class="text-muted-small">
                                            {{ $movimentacao->variacao->cor ?: '-' }}
                                            {{ $movimentacao->variacao->tamanho ?: '-' }}
                                            @if($movimentacao->variacao->sku)
                                                | SKU: {{ $movimentacao->variacao->sku }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="history-cell__sub">Sem variação</div>
                                    @endif
                                </td>

                                <td data-label="Tipo">
                                    @if ($movimentacao->tipo_movimentacao === 'entrada')
                                        <span class="badge-status badge-success">Entrada</span>
                                    @else
                                        <span class="badge-status badge-warning">Ajuste</span>
                                    @endif
                                </td>

                                <td data-label="Qtd">
                                    <span class="number-strong">
                                        {{ number_format((float) $movimentacao->quantidade, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td data-label="Antes">
                                    <span class="number-strong">
                                        {{ number_format((float) $movimentacao->quantidade_anterior, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td data-label="Depois">
                                    <span class="number-strong">
                                        {{ number_format((float) $movimentacao->quantidade_posterior, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td data-label="Usuário">
                                    <div class="history-cell__name">{{ $movimentacao->usuario->name ?? '-' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-clock-history"></i>
                                        <div class="empty-state__title">Nenhuma movimentação encontrada</div>
                                        <div>Não existem registros para os filtros selecionados.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movimentacoes->hasPages())
                <div class="estoque-pagination-wrap">
                    {{ $movimentacoes->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection