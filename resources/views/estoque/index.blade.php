@extends('layouts.app')

@section('title', 'Gerenciamento de Estoque')
@section('pageTitle', 'Gerenciamento de Estoque')
@section('pageDescription', 'Controle o estoque central e o estoque das obras.')

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

    .btn-primary-soft {
        background: var(--accent-soft);
        color: #93c5fd;
        border-color: rgba(13, 110, 253, 0.25);
    }

    .btn-primary-soft:hover {
        background: rgba(13, 110, 253, 0.18);
        border-color: rgba(13, 110, 253, 0.4);
    }

    .btn-warning-soft {
        background: var(--warning-soft);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.20);
    }

    .btn-warning-soft:hover {
        background: rgba(245, 158, 11, 0.18);
        border-color: rgba(245, 158, 11, 0.34);
    }

    .btn-danger-soft {
        background: var(--danger-soft);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.20);
    }

    .btn-danger-soft:hover {
        background: rgba(239, 68, 68, 0.18);
        border-color: rgba(239, 68, 68, 0.34);
    }

    .btn-icon {
        width: 38px;
        height: 38px;
        padding: 0;
        border-radius: 10px;
        font-size: 15px;
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

    .filtros-grid-estoque {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: end;
    }

    .filters-actions-estoque {
        display: flex;
        gap: 10px;
        align-items: end;
        justify-content: flex-end;
        flex-wrap: nowrap;
    }

    .filters-actions-estoque .btn {
        height: 48px;
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

    .table thead th:nth-child(4),
    .table tbody td:nth-child(4) {
        text-align: right;
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

    .linha-inativa td {
        background: rgba(245, 158, 11, 0.04);
    }

    .produto-main {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .produto-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.22), rgba(13, 110, 253, 0.08));
        color: #6ea8fe;
        border: 1px solid rgba(13, 110, 253, 0.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .produto-main__name {
        font-weight: 700;
        color: var(--text-strong);
        line-height: 1.2;
    }

    .produto-main__sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .variacao-box {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .variacao-nome {
        font-weight: 700;
        font-size: 14px;
        line-height: 1.2;
        color: var(--text-strong);
    }

    .variacao-meta {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.3;
    }

    .ca-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: rgba(59, 130, 246, 0.14);
        border: 1px solid rgba(59, 130, 246, 0.28);
        color: #bfdbfe;
    }

    .ca-vazio {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.65);
    }

    .quantidade-box {
        display: inline-flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .quantidade-numero {
        font-size: 24px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.02em;
        color: var(--text-strong);
    }

    .quantidade-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
    }

    .status-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 6px;
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

    .text-muted-small {
        font-size: 12px;
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

    .custom-modal {
        position: fixed;
        inset: 0;
        display: none;
        z-index: 9999;
    }

    .custom-modal.is-open {
        display: block;
    }

    .custom-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(2, 6, 23, 0.70);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .custom-modal-dialog {
        position: relative;
        width: min(1100px, calc(100% - 24px));
        margin: auto;
        top: 50%;
        transform: translateY(-50%);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        background: #0f172a;
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        color: var(--text);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
        z-index: 2;
    }

    .custom-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px 18px;
        background: linear-gradient(180deg, rgba(13,110,253,0.05), transparent 70%);
        border-bottom: 1px solid var(--border);
        position: relative;
    }

    .custom-modal-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
        opacity: 0.55;
    }

    .custom-modal-header h3 {
        margin: 0 0 4px;
        color: var(--text-strong);
        font-size: 19px;
        font-weight: 700;
    }

    .custom-modal-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13.5px;
        line-height: 1.45;
    }

    .custom-modal-close {
        border: 1px solid var(--border-strong);
        background: rgba(15, 23, 42, 0.7);
        color: var(--text);
        width: 36px;
        height: 36px;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color var(--t-med) var(--ease), transform var(--t-fast) var(--ease);
    }

    .custom-modal-close:hover {
        background: #1e293b;
        transform: rotate(90deg);
    }

    .custom-modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
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

    .section-spacer {
        height: 1px;
        background: var(--border-soft);
        margin: 18px 0;
    }

    .modal-open {
        overflow: hidden;
    }

    @media (max-width: 1100px) {
        .filtros-grid-estoque {
            grid-template-columns: 1fr;
        }

        .filters-actions-estoque {
            justify-content: flex-start;
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

        .custom-modal-dialog {
            width: calc(100% - 12px);
            max-height: 92vh;
            border-radius: 18px;
        }

        .custom-modal-header {
            padding: 18px 16px 14px;
        }

        .custom-modal-body {
            padding: 16px;
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

        .quantidade-box {
            align-items: flex-start;
        }

        .quantidade-numero {
            font-size: 28px;
        }

        .ca-badge {
            min-width: unset;
        }

        .filters-actions-estoque {
            flex-direction: column;
            width: 100%;
        }

        .filters-actions-estoque .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (min-width: 821px) {
        .label-mobile {
            display: none;
        }
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-boxes"></i></div>
            <div class="page-head__text">
                <h2>Controle de estoque</h2>
                <p>Sem filtro você vê o estoque central. Ao filtrar, vê o estoque individual da obra.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('estoque.historico') }}" class="btn btn-dark">
                <i class="bi bi-clock-history"></i>
                Histórico de Estoque
            </a>

            <button type="button" class="btn btn-green" onclick="openReabastecerModal()">
                <i class="bi bi-plus-circle"></i>
                Movimentar estoque
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-box">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any() && !session('open_reabastecer_modal'))
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card" style="margin-bottom: 16px;">
        <div class="card-header">
            <div>
                <div class="card-title">Filtro de visualização</div>
                <div class="card-subtitle">Escolha entre estoque central ou estoque individual por obra.</div>
            </div>
        </div>

        <div class="filters-wrap" style="border-bottom: none;">
            <form method="GET" action="{{ route('estoque.index') }}">
                <div class="filtros-grid-estoque">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-building"></i>
                            Visualizar estoque
                        </label>
                        <select name="obra_id" class="form-control-custom" onchange="this.form.submit()">
                            <option value="">Estoque central</option>
                            @foreach ($obras as $obra)
                                <option value="{{ $obra->id }}" {{ (string) $obraId === (string) $obra->id ? 'selected' : '' }}>
                                    {{ $obra->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filters-actions-estoque">
                        <a href="{{ route('estoque.index') }}" class="btn btn-dark">
                            <i class="bi bi-arrow-clockwise"></i>
                            Voltar para central
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">
                    @if ($obraId)
                        Estoque da obra selecionada
                    @else
                        Estoque central
                    @endif
                </div>
                <div class="card-subtitle">
                    @if ($obraId)
                        Exibindo apenas o saldo da obra selecionada.
                    @else
                        Exibindo apenas o saldo do estoque central.
                    @endif
                </div>
            </div>

            <span class="card-count">
                {{ $estoqueTabela->count() }} {{ $estoqueTabela->count() === 1 ? 'item' : 'itens' }}
            </span>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>CA</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($estoqueTabela as $item)
                            @php
                                $produto = $item->produto;
                                $variacao = $item->variacao;

                                $caExibido = $variacao->ca ?? $produto->ca ?? null;

                                $produtoInativo = ($produto->status ?? null) === 'inativo';
                                $variacaoInativa = ($variacao->status ?? null) === 'inativo';
                                $linhaInativa = $produtoInativo || $variacaoInativa;
                            @endphp

                            <tr class="{{ $linhaInativa ? 'linha-inativa' : '' }}">
                                <td data-label="Produto">
                                    <div class="produto-main">
                                        <span class="produto-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </span>

                                        <div>
                                            <div class="produto-main__name">
                                                {{ $produto->nome ?? '-' }}
                                            </div>

                                            <div class="produto-main__sub">
                                                {{ $produto->unidade ?? '-' }}
                                            </div>

                                            @if ($produtoInativo)
                                                <div class="status-inline">
                                                    <span class="badge-status badge-warning">Produto inativo</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Variação">
                                    @if ($variacao)
                                        <div class="variacao-box">
                                            <div class="variacao-nome">
                                                {{ $variacao->nome_variacao }}
                                            </div>

                                            <div class="variacao-meta">
                                                @if ($variacao->cor || $variacao->tamanho)
                                                    {{ $variacao->cor ?: 'Sem cor' }} / {{ $variacao->tamanho ?: 'Sem tamanho' }}
                                                @else
                                                    Sem detalhes adicionais
                                                @endif
                                            </div>

                                            @if (!empty($variacao->sku))
                                                <div class="variacao-meta">
                                                    SKU: {{ $variacao->sku }}
                                                </div>
                                            @endif

                                            @if ($variacaoInativa)
                                                <div class="status-inline">
                                                    <span class="badge-status badge-warning">Variação inativa</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="variacao-box">
                                            <div class="variacao-nome">Sem variação</div>
                                            <div class="variacao-meta">Produto simples</div>
                                        </div>
                                    @endif
                                </td>

                                <td data-label="CA">
                                    @if ($caExibido)
                                        <span class="ca-badge">{{ $caExibido }}</span>
                                    @else
                                        <span class="ca-badge ca-vazio">Sem CA</span>
                                    @endif
                                </td>
                                                                <td data-label="Quantidade">
                                    <div class="quantidade-box">
                                        <div class="quantidade-numero">
                                            {{ number_format((float) $item->quantidade_total, 0, ',', '.') }}
                                        </div>
                                        <div class="quantidade-label">em estoque</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-box-seam"></i>
                                        <div class="empty-state__title">Nenhum saldo registrado</div>
                                        <div>Não existem itens com saldo para a visualização selecionada.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="custom-modal" id="reabastecerModal" role="dialog" aria-modal="true" aria-labelledby="reabastecerTitle">
        <div class="custom-modal-backdrop" onclick="closeReabastecerModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="reabastecerTitle">Movimentar estoque</h3>
                    <p>Entrada e ajuste mexem no estoque central. Transferência pode enviar do central para obra ou entre obras.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="closeReabastecerModal()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                @if ($errors->any() && session('open_reabastecer_modal'))
                    <div class="alert-error-box">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('estoque.reabastecer') }}" method="POST">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-arrow-left-right"></i>
                                Tipo
                            </label>
                            <select
                                name="tipo_movimentacao"
                                id="tipo_movimentacao"
                                class="form-control-custom"
                                required
                                onchange="toggleCamposMovimentacao()"
                            >
                                <option value="entrada" {{ old('tipo_movimentacao') === 'entrada' ? 'selected' : '' }}>
                                    Entrada no estoque central
                                </option>
                                <option value="ajuste" {{ old('tipo_movimentacao') === 'ajuste' ? 'selected' : '' }}>
                                    Ajuste no estoque central
                                </option>
                                <option value="transferencia" {{ old('tipo_movimentacao') === 'transferencia' ? 'selected' : '' }}>
                                    Transferência do central para obra
                                </option>
                                <option value="transferencia_entre_obras" {{ old('tipo_movimentacao') === 'transferencia_entre_obras' ? 'selected' : '' }}>
                                    Transferência entre obras
                                </option>
                            </select>
                        </div>

                        <div class="form-group" id="grupo_obra_destino_central">
                            <label class="form-label">
                                <i class="bi bi-building"></i>
                                Obra de destino
                            </label>
                            <select name="obra_id" class="form-control-custom">
                                <option value="">Selecione</option>
                                @foreach ($obras as $obra)
                                    <option value="{{ $obra->id }}" {{ old('obra_id', $obraId) == $obra->id ? 'selected' : '' }}>
                                        {{ $obra->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="grupo_obra_origem">
                            <label class="form-label">
                                <i class="bi bi-building"></i>
                                Obra de origem
                            </label>
                            <select name="obra_origem_id" class="form-control-custom">
                                <option value="">Selecione</option>
                                @foreach ($obras as $obra)
                                    <option value="{{ $obra->id }}" {{ old('obra_origem_id') == $obra->id ? 'selected' : '' }}>
                                        {{ $obra->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="grupo_obra_destino_obra">
                            <label class="form-label">
                                <i class="bi bi-building"></i>
                                Obra de destino
                            </label>
                            <select name="obra_destino_id" class="form-control-custom">
                                <option value="">Selecione</option>
                                @foreach ($obras as $obra)
                                    <option value="{{ $obra->id }}" {{ old('obra_destino_id') == $obra->id ? 'selected' : '' }}>
                                        {{ $obra->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-calendar3"></i>
                                Data da movimentação
                            </label>
                            <input
                                type="date"
                                name="data_movimentacao"
                                class="form-control-custom"
                                value="{{ old('data_movimentacao', now()->toDateString()) }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="section-spacer"></div>

                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Variação</th>
                                    <th>Disponível no central</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $linha = 0; @endphp

                                @foreach ($produtosMovimentacao as $produto)
                                    @if ($produto->controla_variacao && $produto->variacoes->count())
                                        @foreach ($produto->variacoes as $variacao)
                                            @php
                                                $chaveCentral = $produto->id . '-' . $variacao->id;
                                                $estoqueCentral = isset($estoquesCentrais[$chaveCentral])
                                                    ? $estoquesCentrais[$chaveCentral]->quantidade_atual
                                                    : 0;
                                            @endphp

                                            <tr>
                                                <td data-label="Produto">
                                                    <div class="produto-main">
                                                        <span class="produto-icon">
                                                            <i class="bi bi-box-seam"></i>
                                                        </span>
                                                        <div>
                                                            <div class="produto-main__name">{{ $produto->nome }}</div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="itens[{{ $linha }}][produto_id]" value="{{ $produto->id }}">
                                                </td>

                                                <td data-label="Variação">
                                                    <div class="variacao-box">
                                                        <div class="variacao-nome">{{ $variacao->nome_variacao }}</div>

                                                        <div class="variacao-meta">
                                                            {{ $variacao->cor ?? '' }}
                                                            {{ $variacao->tamanho ?? '' }}
                                                            @if($variacao->sku)
                                                                | SKU: {{ $variacao->sku }}
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="itens[{{ $linha }}][produto_variacao_id]" value="{{ $variacao->id }}">
                                                </td>

                                                <td data-label="Disponível no central">
                                                    <div class="quantidade-box" style="align-items:flex-start;">
                                                        <div class="quantidade-numero" style="font-size:18px;">
                                                            {{ number_format((float) $estoqueCentral, 0, ',', '.') }}
                                                        </div>
                                                        <div class="quantidade-label">disponível</div>
                                                    </div>
                                                </td>

                                                <td data-label="Quantidade">
                                                    <input
                                                        type="number"
                                                        step="1"
                                                        min="0"
                                                        name="itens[{{ $linha }}][quantidade]"
                                                        class="form-control-custom"
                                                        value="{{ old('itens.' . $linha . '.quantidade') }}"
                                                        placeholder="0"
                                                    >
                                                </td>
                                            </tr>
                                            @php $linha++; @endphp
                                        @endforeach
                                    @else
                                        @php
                                            $chaveCentral = $produto->id . '-null';
                                            $estoqueCentral = isset($estoquesCentrais[$chaveCentral])
                                                ? $estoquesCentrais[$chaveCentral]->quantidade_atual
                                                : 0;
                                        @endphp

                                        <tr>
                                            <td data-label="Produto">
                                                <div class="produto-main">
                                                    <span class="produto-icon">
                                                        <i class="bi bi-box-seam"></i>
                                                    </span>
                                                    <div>
                                                        <div class="produto-main__name">{{ $produto->nome }}</div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="itens[{{ $linha }}][produto_id]" value="{{ $produto->id }}">
                                            </td>

                                            <td data-label="Variação">
                                                <div class="variacao-box">
                                                    <div class="variacao-nome">Sem variação</div>
                                                    <div class="variacao-meta">Produto simples</div>
                                                </div>
                                                <input type="hidden" name="itens[{{ $linha }}][produto_variacao_id]" value="">
                                            </td>

                                            <td data-label="Disponível no central">
                                                <div class="quantidade-box" style="align-items:flex-start;">
                                                    <div class="quantidade-numero" style="font-size:18px;">
                                                        {{ number_format((float) $estoqueCentral, 0, ',', '.') }}
                                                    </div>
                                                    <div class="quantidade-label">disponível</div>
                                                </div>
                                            </td>

                                            <td data-label="Quantidade">
                                                <input
                                                    type="number"
                                                    step="1"
                                                    min="0"
                                                    name="itens[{{ $linha }}][quantidade]"
                                                    class="form-control-custom"
                                                    value="{{ old('itens.' . $linha . '.quantidade') }}"
                                                    placeholder="0"
                                                >
                                            </td>
                                        </tr>
                                        @php $linha++; @endphp
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="section-spacer"></div>

                    <div class="actions-inline" style="justify-content:flex-end;">
                        <button type="button" class="btn btn-dark" onclick="closeReabastecerModal()">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-green">
                            <i class="bi bi-check2-circle"></i>
                            Salvar movimentação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let lastFocused = null;

        function openModal(modal) {
            if (!modal) return;

            lastFocused = document.activeElement;
            modal.classList.add('is-open');
            document.body.classList.add('modal-open');

            const firstInput = modal.querySelector('input, select, textarea, button');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 50);
            }
        }

        function closeModal(modal) {
            if (!modal) return;

            modal.classList.remove('is-open');

            if (!document.querySelector('.custom-modal.is-open')) {
                document.body.classList.remove('modal-open');
            }

            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }

        function openReabastecerModal() {
            openModal(document.getElementById('reabastecerModal'));
            toggleCamposMovimentacao();
        }

        function closeReabastecerModal() {
            closeModal(document.getElementById('reabastecerModal'));
        }

        function toggleCamposMovimentacao() {
            const tipo = document.getElementById('tipo_movimentacao')?.value;

            const grupoDestinoCentral = document.getElementById('grupo_obra_destino_central');
            const grupoOrigem = document.getElementById('grupo_obra_origem');
            const grupoDestinoObra = document.getElementById('grupo_obra_destino_obra');

            if (!grupoDestinoCentral || !grupoOrigem || !grupoDestinoObra) return;

            grupoDestinoCentral.style.display = 'none';
            grupoOrigem.style.display = 'none';
            grupoDestinoObra.style.display = 'none';

            if (tipo === 'transferencia') {
                grupoDestinoCentral.style.display = 'block';
            }

            if (tipo === 'transferencia_entre_obras') {
                grupoOrigem.style.display = 'block';
                grupoDestinoObra.style.display = 'block';
            }
        }

        function initEstoquePage() {
            toggleCamposMovimentacao();

            @if (session('open_reabastecer_modal'))
                openReabastecerModal();
            @endif
        }

        document.addEventListener('DOMContentLoaded', initEstoquePage);
        document.addEventListener('page:updated', initEstoquePage);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeReabastecerModal();
            }
        });
    </script>
@endsection