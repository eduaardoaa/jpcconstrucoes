@extends('layouts.app')

@section('title', 'Entregas de EPI')
@section('pageTitle', 'Entregas de EPI')
@section('pageDescription', 'Lista de funcionários e resumo das entregas de EPI.')

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

    textarea.form-control-custom {
        min-height: 92px;
        resize: vertical;
        line-height: 1.5;
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

    .linha-funcionario-inativo td {
        background: rgba(255, 80, 80, 0.08) !important;
        border-color: rgba(255, 80, 80, 0.18) !important;
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

    .badge-danger {
        background: rgba(255, 80, 80, .18);
        color: #ffb3b3;
        border: 1px solid rgba(255, 80, 80, .35);
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

    .btn-disabled-custom {
        opacity: .55;
        cursor: not-allowed;
        pointer-events: none;
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

    .section-spacer {
        height: 1px;
        background: var(--border-soft);
        margin: 18px 0;
    }

    .modal-open {
        overflow: hidden;
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

        .table-actions .btn,
        .table-actions a {
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
            <div class="page-head__icon"><i class="bi bi-box2-heart"></i></div>
            <div class="page-head__text">
                <h2>Entregas de EPI</h2>
                <p>Visualize todos os funcionários, a obra vinculada e a última entrega registrada.</p>
            </div>
        </div>

        <div class="actions-inline">
            <button type="button" class="btn btn-green" onclick="openCreateModal()">
                <i class="bi bi-plus-circle"></i>
                Nova entrega
            </button>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <div>
                <div class="card-title">Filtros</div>
                <div class="card-subtitle">Pesquise funcionários e filtre por obra.</div>
            </div>
        </div>

        <div class="filters-wrap" style="border-bottom:none;">
            <form method="GET" action="{{ route('entregas.index') }}" id="formFiltrosEntregas">
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label class="form-label">
                            <i class="bi bi-search"></i>
                            Pesquisar funcionário
                        </label>
                        <input
                            type="text"
                            name="search"
                            id="filtroBuscaEntregas"
                            class="form-control-custom"
                            placeholder="Digite o nome, matrícula ou CPF"
                            value="{{ $search }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-building"></i>
                            Obra
                        </label>
                        <select name="obra_id" id="filtroObraEntregas" class="form-control-custom">
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
                            <i class="bi bi-arrow-clockwise"></i>
                            Ações
                        </label>
                        <div class="actions-inline">
                            <a href="{{ route('entregas.index') }}" class="btn btn-dark">
                                <i class="bi bi-x-circle"></i>
                                Limpar filtros
                            </a>
                        </div>
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

    @if($errors->any() && !session('open_create_modal'))
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Funcionários</div>
                <div class="card-subtitle">Todos os funcionários aparecem aqui, mesmo sem nenhuma entrega registrada.</div>
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
                            <th>Comprovante</th>
                            <th style="text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funcionarios as $funcionario)
                            @php
                                $ultimaEntrega = $funcionario->ultima_entrega;
                                $ultimoComprovante = $funcionario->ultimo_comprovante;
                                $funcionarioInativo = ($funcionario->status ?? null) !== 'ativo';

                                $iniciais = collect(explode(' ', trim($funcionario->nome)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_substr($p, 0, 1))
                                    ->implode('');
                            @endphp

                            <tr class="{{ $funcionarioInativo ? 'linha-funcionario-inativo' : '' }}">
                                <td data-label="Funcionário">
                                    <div class="user-cell">
                                        <div class="avatar">{{ $iniciais ?: 'F' }}</div>
                                        <div>
                                            <div class="user-cell__name">{{ $funcionario->nome }}</div>

                                            @if($funcionarioInativo)
                                                <div style="margin-top:6px;">
                                                    <span class="badge-status badge-danger">Funcionário inativo</span>
                                                </div>
                                            @endif

                                            <div class="user-cell__sub">Matrícula: {{ $funcionario->matricula ?: '-' }}</div>
                                            <div class="user-cell__sub">CPF: {{ $funcionario->cpf ?: '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Obra">{{ $funcionario->obra->nome ?? '-' }}</td>

                                <td data-label="Cargo">{{ $funcionario->cargo->nome ?? '-' }}</td>

                                <td data-label="Última entrega">
                                    @if($ultimaEntrega)
                                        <strong>{{ $ultimaEntrega->data_entrega?->format('d/m/Y') }}</strong>
                                        <div class="text-muted-small">Entrega #{{ $ultimaEntrega->id }}</div>
                                    @else
                                        <span class="text-muted-small">Nenhuma entrega</span>
                                    @endif
                                </td>

                                <td data-label="Itens da última entrega">
                                    @if($ultimaEntrega && $ultimaEntrega->itens->count())
                                        <div class="simple-list">
                                            @foreach($ultimaEntrega->itens as $item)
                                                <div class="simple-item">
                                                    <div class="simple-item__title">{{ $item->produto->nome ?? '-' }}</div>

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

                                <td data-label="Comprovante">
                                    @if($funcionarioInativo)
                                        <span class="badge-status badge-danger">Bloqueado</span>
                                    @elseif($ultimaEntrega)
                                        @if($ultimaEntrega->status_comprovante === 'anexado')
                                            <span class="badge-status badge-success">Anexado</span>
                                        @else
                                            <span class="badge-status badge-warning">Pendente</span>
                                        @endif
                                    @else
                                        <span class="badge-status badge-info">Sem entrega</span>
                                    @endif
                                </td>

                                <td data-label="Ações" style="text-align:right;">
                                    <div class="table-actions">
                                        @if($funcionarioInativo)
                                            <button
                                                type="button"
                                                class="btn btn-dark btn-disabled-custom"
                                                disabled
                                                title="Funcionário inativo não pode receber nova entrega"
                                            >
                                                Entregar
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                class="btn btn-green"
                                                onclick="openCreateModal('{{ $funcionario->obra_id }}', '{{ $funcionario->id }}')"
                                            >
                                                <i class="bi bi-plus-circle"></i>
                                                Entregar
                                            </button>
                                        @endif

                                        @if(Route::has('epi.historico'))
                                            <a href="{{ route('epi.historico', $funcionario->id) }}" class="btn btn-dark">
                                                <i class="bi bi-clock-history"></i>
                                                Histórico
                                            </a>
                                        @endif

                                        @if($ultimaEntrega && Route::has('epi.pdf.ultima'))
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
                                        <i class="bi bi-box2-heart"></i>
                                        <div class="empty-state__title">Nenhum funcionário encontrado</div>
                                        <div>Tente ajustar os filtros ou cadastrar novos funcionários.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL NOVA ENTREGA --}}
    <div class="custom-modal" id="createEntregaModal" role="dialog" aria-modal="true" aria-labelledby="createEntregaTitle">
        <div class="custom-modal-backdrop" onclick="closeCreateModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="createEntregaTitle">Nova entrega de EPI</h3>
                    <p>Selecione a obra, o funcionário e informe as quantidades dos itens.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="closeCreateModal()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                @if ($errors->any() && session('open_create_modal'))
                    <div class="alert-error-box">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('entregas.store') }}" method="POST">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-building"></i>
                                Obra
                            </label>
                            <select
                                name="obra_id"
                                id="create_obra_id"
                                class="form-control-custom"
                                onchange="filtrarFuncionariosPorObra(); atualizarEstoquesPorObra();"
                                required
                            >
                                <option value="">Selecione</option>
                                @foreach ($obrasAtivas as $obra)
                                    <option value="{{ $obra->id }}">
                                        {{ $obra->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-person"></i>
                                Funcionário
                            </label>
                            <select
                                name="funcionario_id"
                                id="create_funcionario_id"
                                class="form-control-custom"
                                required
                            >
                                <option value="">Selecione uma obra primeiro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-calendar3"></i>
                                Data da entrega
                            </label>
                            <input
                                type="date"
                                name="data_entrega"
                                class="form-control-custom"
                                value="{{ old('data_entrega', now()->toDateString()) }}"
                                required
                            >
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-chat-left-text"></i>
                                Observações
                            </label>
                            <textarea name="observacoes" class="form-control-custom" rows="3">{{ old('observacoes') }}</textarea>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-arrow-return-left"></i>
                                Motivo da devolução
                            </label>
                            <textarea
                                name="motivo_devolucao"
                                id="motivo_devolucao"
                                class="form-control-custom"
                                rows="3"
                                placeholder="Ex.: equipamento danificado, troca de tamanho, substituição, desligamento..."
                            >{{ old('motivo_devolucao') }}</textarea>
                        </div>
                    </div>

                    <div class="section-spacer"></div>

                    <div class="card" style="margin-bottom:16px; border:1px solid rgba(255,255,255,.08);">
                        <div class="card-header">
                            <div>
                                <div class="card-title" style="font-size:16px;">Itens pendentes para devolução</div>
                                <div class="card-subtitle" id="ultima-entrega-info">Selecione um funcionário para visualizar.</div>
                            </div>
                        </div>

                        <div class="card-body" style="padding:16px;">
                            <div id="sem-ultima-entrega" class="text-muted-small">
                                Este funcionário não possui itens pendentes de devolução.
                            </div>

                            <div class="table-wrap" id="bloco-devolucao" style="display:none;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Variação</th>
                                            <th>Qtd. pendente</th>
                                            <th>Qtd. devolvida agora</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-devolucao"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="section-spacer"></div>

                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Variação</th>
                                    <th>Estoque atual</th>
                                    <th>Quantidade para entregar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $linha = 0; @endphp
                                @foreach ($produtos as $produto)
                                    @if ($produto->controla_variacao && $produto->variacoes->count())
                                        @foreach ($produto->variacoes as $variacao)
                                            <tr>
                                                <td data-label="Produto">
                                                    {{ $produto->nome }}
                                                    <input type="hidden" name="itens[{{ $linha }}][produto_id]" value="{{ $produto->id }}">
                                                </td>
                                                <td data-label="Variação">
                                                    <strong>{{ $variacao->nome_variacao }}</strong>
                                                    <div class="text-muted-small">
                                                        {{ $variacao->cor ?? '' }}
                                                        {{ $variacao->tamanho ?? '' }}
                                                        @if($variacao->sku)
                                                            | SKU: {{ $variacao->sku }}
                                                        @endif
                                                    </div>
                                                    <input type="hidden" name="itens[{{ $linha }}][produto_variacao_id]" value="{{ $variacao->id }}">
                                                </td>
                                                <td data-label="Estoque atual">
                                                    <span class="estoque-atual" data-produto-id="{{ $produto->id }}" data-variacao-id="{{ $variacao->id }}">0</span>
                                                </td>
                                                <td data-label="Quantidade para entregar">
                                                    <input
                                                        type="number"
                                                        name="itens[{{ $linha }}][quantidade]"
                                                        class="form-control-custom"
                                                        min="0"
                                                        step="1"
                                                        value="{{ old('itens.' . $linha . '.quantidade') }}"
                                                        placeholder="0"
                                                    >
                                                </td>
                                            </tr>
                                            @php $linha++; @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td data-label="Produto">
                                                {{ $produto->nome }}
                                                <input type="hidden" name="itens[{{ $linha }}][produto_id]" value="{{ $produto->id }}">
                                            </td>
                                            <td data-label="Variação">
                                                Sem variação
                                                <input type="hidden" name="itens[{{ $linha }}][produto_variacao_id]" value="">
                                            </td>
                                            <td data-label="Estoque atual">
                                                <span class="estoque-atual" data-produto-id="{{ $produto->id }}" data-variacao-id="">0</span>
                                            </td>
                                            <td data-label="Quantidade para entregar">
                                                <input
                                                    type="number"
                                                    name="itens[{{ $linha }}][quantidade]"
                                                    class="form-control-custom"
                                                    min="0"
                                                    step="1"
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
                        <button type="button" class="btn btn-dark" onclick="closeCreateModal()">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-green">
                            <i class="bi bi-check2-circle"></i>
                            Salvar entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $funcionariosData = $funcionariosModal->map(function ($funcionario) {
            return [
                'id' => $funcionario->id,
                'nome' => $funcionario->nome,
                'matricula' => $funcionario->matricula,
                'obra_id' => $funcionario->obra_id,
                'status' => $funcionario->status,
                'itens_pendentes_devolucao' => collect($funcionario->itens_pendentes_devolucao ?? [])->values(),
            ];
        })->values();
    @endphp

    <script>
        window.funcionariosData = @json($funcionariosData);
        window.estoqueMapa = @json($estoqueMapa);

        (function () {
            let lastFocused = null;

            window.openModal = function (modal) {
                if (!modal) return;

                lastFocused = document.activeElement;
                modal.classList.add('is-open');
                document.body.classList.add('modal-open');

                const firstInput = modal.querySelector('input, select, textarea, button');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 50);
                }
            };

            window.closeModal = function (modal) {
                if (!modal) return;

                modal.classList.remove('is-open');

                if (!document.querySelector('.custom-modal.is-open')) {
                    document.body.classList.remove('modal-open');
                }

                if (lastFocused && typeof lastFocused.focus === 'function') {
                    lastFocused.focus();
                }
            };

            window.openCreateModal = function (obraId = '', funcionarioId = '') {
                const modal = document.getElementById('createEntregaModal');
                const obraSelect = document.getElementById('create_obra_id');

                if (!modal) return;

                window.openModal(modal);

                if (obraSelect) {
                    obraSelect.value = obraId || '';
                }

                window.filtrarFuncionariosPorObra(funcionarioId);
                window.atualizarEstoquesPorObra();
                window.renderizarUltimaEntregaParaDevolucao();
            };

            window.closeCreateModal = function () {
                window.closeModal(document.getElementById('createEntregaModal'));
            };

            window.filtrarFuncionariosPorObra = function (funcionarioSelecionado = '') {
                const obraSelect = document.getElementById('create_obra_id');
                const funcionarioSelect = document.getElementById('create_funcionario_id');

                if (!obraSelect || !funcionarioSelect) return;

                const obraIdAtual = obraSelect.value;
                funcionarioSelect.innerHTML = '';

                if (!obraIdAtual) {
                    funcionarioSelect.innerHTML = '<option value="">Selecione uma obra primeiro</option>';
                    window.renderizarUltimaEntregaParaDevolucao();
                    return;
                }

                const funcionariosFiltrados = (window.funcionariosData || []).filter(funcionario =>
                    String(funcionario.obra_id) === String(obraIdAtual) &&
                    String(funcionario.status) === 'ativo'
                );

                if (funcionariosFiltrados.length === 0) {
                    funcionarioSelect.innerHTML = '<option value="">Nenhum funcionário ativo vinculado a esta obra</option>';
                    window.renderizarUltimaEntregaParaDevolucao();
                    return;
                }

                const optionPadrao = document.createElement('option');
                optionPadrao.value = '';
                optionPadrao.textContent = 'Selecione';
                funcionarioSelect.appendChild(optionPadrao);

                funcionariosFiltrados.forEach(funcionario => {
                    const option = document.createElement('option');
                    option.value = funcionario.id;
                    option.textContent = funcionario.matricula
                        ? `${funcionario.nome} - ${funcionario.matricula}`
                        : funcionario.nome;

                    if (String(funcionario.id) === String(funcionarioSelecionado)) {
                        option.selected = true;
                    }

                    funcionarioSelect.appendChild(option);
                });

                window.renderizarUltimaEntregaParaDevolucao();
            };

            window.atualizarEstoquesPorObra = function () {
                const obraIdAtual = document.getElementById('create_obra_id')?.value;
                const spans = document.querySelectorAll('.estoque-atual');

                spans.forEach(span => {
                    const produtoId = span.dataset.produtoId;
                    const variacaoId = span.dataset.variacaoId ? span.dataset.variacaoId : 'null';

                    if (!obraIdAtual) {
                        span.textContent = '0';
                        return;
                    }

                    const chave = `${obraIdAtual}-${produtoId}-${variacaoId}`;
                    span.textContent = window.estoqueMapa?.[chave] ?? 0;
                });
            };

            window.renderizarUltimaEntregaParaDevolucao = function () {
                const funcionarioSelect = document.getElementById('create_funcionario_id');
                const bloco = document.getElementById('bloco-devolucao');
                const tbody = document.getElementById('tbody-devolucao');
                const semUltimaEntrega = document.getElementById('sem-ultima-entrega');
                const info = document.getElementById('ultima-entrega-info');

                if (!funcionarioSelect || !tbody || !bloco || !semUltimaEntrega || !info) {
                    return;
                }

                tbody.innerHTML = '';

                const funcionarioId = funcionarioSelect.value;

                if (!funcionarioId) {
                    bloco.style.display = 'none';
                    semUltimaEntrega.style.display = 'block';
                    semUltimaEntrega.textContent = 'Selecione um funcionário para visualizar os itens pendentes de devolução.';
                    info.textContent = 'Selecione um funcionário para visualizar.';
                    return;
                }

                const funcionario = (window.funcionariosData || []).find(item => String(item.id) === String(funcionarioId));

                if (!funcionario || !funcionario.itens_pendentes_devolucao || !funcionario.itens_pendentes_devolucao.length) {
                    bloco.style.display = 'none';
                    semUltimaEntrega.style.display = 'block';
                    semUltimaEntrega.textContent = 'Este funcionário não possui itens pendentes de devolução.';
                    info.textContent = 'Nenhum item pendente de devolução.';
                    return;
                }

                bloco.style.display = 'block';
                semUltimaEntrega.style.display = 'none';
                info.textContent = 'Itens ainda pendentes de devolução para este funcionário.';

                funcionario.itens_pendentes_devolucao.forEach((item, index) => {
                    const variacaoTexto = item.variacao_nome
                        ? `<strong>${item.variacao_nome}</strong><div class="text-muted-small">${item.cor ?? ''} ${item.tamanho ?? ''} ${item.sku ? '| SKU: ' + item.sku : ''}</div>`
                        : 'Sem variação';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td data-label="Produto">
                            ${item.produto_nome}
                            <input type="hidden" name="devolucoes[${index}][produto_id]" value="${item.produto_id}">
                        </td>
                        <td data-label="Variação">
                            ${variacaoTexto}
                            <input type="hidden" name="devolucoes[${index}][produto_variacao_id]" value="${item.produto_variacao_id ?? ''}">
                        </td>
                        <td data-label="Qtd. pendente">
                            ${item.quantidade_pendente}
                        </td>
                        <td data-label="Qtd. devolvida agora">
                            <input
                                type="number"
                                name="devolucoes[${index}][quantidade]"
                                class="form-control-custom"
                                min="0"
                                max="${item.quantidade_pendente}"
                                step="1"
                                value="0"
                                placeholder="0"
                            >
                        </td>
                    `;

                    tbody.appendChild(tr);
                });
            };

            function bindEntregasPageEvents() {
                const formFiltrosEntregas = document.getElementById('formFiltrosEntregas');
                const filtroBuscaEntregas = document.getElementById('filtroBuscaEntregas');
                const filtroObraEntregas = document.getElementById('filtroObraEntregas');
                const funcionarioSelectModal = document.getElementById('create_funcionario_id');

                let filtroTimeout = null;

                if (filtroBuscaEntregas && formFiltrosEntregas && !filtroBuscaEntregas.dataset.bound) {
                    filtroBuscaEntregas.dataset.bound = 'true';

                    filtroBuscaEntregas.addEventListener('input', function () {
                        clearTimeout(filtroTimeout);

                        filtroTimeout = setTimeout(function () {
                            formFiltrosEntregas.submit();
                        }, 400);
                    });
                }

                if (filtroObraEntregas && formFiltrosEntregas && !filtroObraEntregas.dataset.bound) {
                    filtroObraEntregas.dataset.bound = 'true';

                    filtroObraEntregas.addEventListener('change', function () {
                        formFiltrosEntregas.submit();
                    });
                }

                if (funcionarioSelectModal && !funcionarioSelectModal.dataset.bound) {
                    funcionarioSelectModal.dataset.bound = 'true';

                    funcionarioSelectModal.addEventListener('change', function () {
                        window.renderizarUltimaEntregaParaDevolucao();
                    });
                }
            }

            function initEntregasPage() {
                bindEntregasPageEvents();

                @if(session('open_create_modal'))
                    window.openCreateModal('{{ old('obra_id') }}', '{{ old('funcionario_id') }}');
                @endif
            }

            document.addEventListener('DOMContentLoaded', initEntregasPage);
            document.addEventListener('page:updated', initEntregasPage);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    window.closeCreateModal();
                }
            });
        })();
    </script>
@endsection