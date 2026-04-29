@extends('layouts.app')

@section('title', 'Gerenciamento de Obras')
@section('pageTitle', 'Gerenciamento de Obras')
@section('pageDescription', 'Gerencie as obras cadastradas no sistema.')

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
        --info: #38bdf8;
        --info-soft: rgba(56, 189, 248, 0.12);

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
        text-decoration: none;
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

    .btn-info-soft {
        background: var(--info-soft);
        color: #7dd3fc;
        border-color: rgba(56, 189, 248, 0.22);
    }

    .btn-info-soft:hover {
        background: rgba(56, 189, 248, 0.18);
    }

    .btn-warning-soft {
        background: var(--warning-soft);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.22);
    }

    .btn-warning-soft:hover {
        background: rgba(245, 158, 11, 0.18);
    }

    .btn-danger-soft {
        background: var(--danger-soft);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.22);
    }

    .btn-danger-soft:hover {
        background: rgba(239, 68, 68, 0.18);
    }

    .btn-icon {
        min-width: 42px;
        min-height: 42px;
        padding: 10px 12px;
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
        background: rgba(255, 255, 255, 0.01);
    }

    .filters-form {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) 220px auto;
        gap: 12px;
        align-items: end;
    }

    .filter-group {
        min-width: 0;
    }

    .filter-label {
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

    .filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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

    .obra-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .obra-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.22), rgba(148, 163, 184, 0.08));
        color: var(--text-strong);
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid var(--border-strong);
    }

    .obra-cell__name {
        font-weight: 600;
        color: var(--text-strong);
        line-height: 1.2;
    }

    .obra-cell__sub {
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
        background: rgba(148, 163, 184, 0.12);
        color: #cbd5e1;
        border-color: rgba(148, 163, 184, 0.18);
    }

    .table-actions {
        display: flex;
        gap: 6px;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .table-actions form {
        margin: 0;
    }

    .tip {
        position: relative;
    }

    .tip::after {
        content: attr(data-tip);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%) translateY(4px);
        background: #020817;
        color: var(--text-strong);
        font-size: 11px;
        font-weight: 600;
        padding: 5px 9px;
        border-radius: 6px;
        border: 1px solid var(--border-strong);
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity var(--t-med) var(--ease), transform var(--t-med) var(--ease);
        z-index: 10;
    }

    .tip:hover::after {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
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
        width: min(720px, calc(100% - 24px));
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
        background: linear-gradient(180deg, rgba(13, 110, 253, 0.05), transparent 70%);
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

    .input-wrap {
        position: relative;
    }

    .input-wrap .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-subtle);
        pointer-events: none;
        font-size: 14px;
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

    .has-icon .form-control-custom {
        padding-left: 40px;
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
        min-height: 120px;
        resize: vertical;
        padding-top: 14px;
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

    .form-divider {
        height: 1px;
        background: var(--border-soft);
        margin: 6px 0 2px;
        grid-column: 1 / -1;
    }

    .form-hint {
        margin-top: 6px;
        font-size: 12px;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

        .filters-form {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions .btn {
            width: 100%;
        }

        .card-header {
            padding: 14px 16px;
            flex-wrap: wrap;
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

        .table td[data-label="Nome"] {
            font-weight: 700;
            color: var(--text-strong);
            font-size: 15px;
            padding-bottom: 6px !important;
            padding-right: 95px !important;
            position: relative;
        }

        .table td[data-label="Nome"]::before {
            display: none;
        }

        .table td[data-label="Status"] {
            position: absolute;
            top: 14px;
            right: 14px;
            width: auto !important;
            padding: 0 !important;
        }

        .table td[data-label="Status"]::before {
            display: none !important;
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

        .table td[data-label="Ações"] {
            padding-top: 12px !important;
            border-top: 1px solid var(--border-soft) !important;
            margin-top: 10px !important;
        }

        .table td[data-label="Ações"]::before {
            display: none;
        }

        .table-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            width: 100%;
        }

        .table-actions .btn,
        .table-actions form,
        .table-actions form button,
        .table-actions a {
            width: 100%;
            justify-content: center;
        }

        .table-actions .btn-icon {
            width: 100%;
            height: auto;
            padding: 10px 14px;
            font-size: 13.5px;
        }

        .table-actions .btn-icon .label-mobile {
            display: inline;
        }

        .tip::after {
            display: none;
        }
    }

    @media (min-width: 821px) {
        .label-mobile {
            display: none;
        }
    }
</style>

    @php
        $obraEditSession = session('open_edit_modal')
            ? $obras->firstWhere('id', session('open_edit_modal'))
            : null;

        $filtrosAtivos = filled($busca) || in_array($status, ['ativa', 'inativa']);
    @endphp

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-building"></i></div>
            <div class="page-head__text">
                <h2>Gerenciar obras</h2>
                <p>Cadastre, edite, ative, inative e exclua obras.</p>
            </div>
        </div>

        <div class="actions-inline">
            <button type="button" class="btn btn-green" onclick="openCreateModal()">
                <i class="bi bi-building-add"></i>
                Nova obra
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-box">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any() && !session('open_create_modal') && !session('open_edit_modal'))
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Lista de obras</div>
                <div class="card-subtitle">Filtre por nome, endereço e status.</div>
            </div>
            <span class="card-count">{{ $obras->count() }} {{ $obras->count() === 1 ? 'obra' : 'obras' }}</span>
        </div>

        <div class="filters-wrap">
            <form method="GET" action="{{ route('obras.index') }}" class="filters-form">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-search"></i> Buscar
                    </label>
                    <div class="input-wrap has-icon">
                        <i class="bi bi-search input-icon"></i>
                        <input
                            type="text"
                            name="busca"
                            class="form-control-custom"
                            value="{{ $busca }}"
                            placeholder="Buscar por nome ou endereço..."
                        >
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-funnel"></i> Status
                    </label>
                    <select name="status" class="form-control-custom">
                        <option value="">Todas</option>
                        <option value="ativa" {{ $status === 'ativa' ? 'selected' : '' }}>Ativas</option>
                        <option value="inativa" {{ $status === 'inativa' ? 'selected' : '' }}>Inativas</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-green">
                        <i class="bi bi-search"></i>
                        Filtrar
                    </button>

                    <a href="{{ route('obras.index') }}" class="btn btn-dark">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Obra</th>
                            <th>Endereço</th>
                            <th>Técnico</th>
                            <th>Início</th>
                            <th>Último DDS</th>
                            <th>Status</th>
                            <th style="text-align:right;">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($obras as $obra)
                            @php
                                $tecnicoNome = optional($tecnicos->firstWhere('id', $obra->responsavel))->name;
                            @endphp

                            <tr>
                                <td data-label="Nome">
                                    <div class="obra-cell">
                                        <span class="obra-icon">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <div>
                                            <div class="obra-cell__name">{{ $obra->nome }}</div>
                                            <div class="obra-cell__sub">
                                                {{ $obra->observacoes ? \Illuminate\Support\Str::limit($obra->observacoes, 45) : 'Sem observações' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Endereço">
                                    {{ $obra->endereco ?: '—' }}
                                </td>

                                <td data-label="Técnico">
                                    {{ $tecnicoNome ?: '—' }}
                                </td>

                                <td data-label="Início">
                                    {{ $obra->data_inicio ? \Carbon\Carbon::parse($obra->data_inicio)->format('d/m/Y') : '—' }}
                                </td>

                                <td data-label="Último DDS">
                                    @if ($obra->ultimoTreinamentoDds?->data_treinamento)
                                        <span class="badge-status badge-info">
                                            {{ $obra->ultimoTreinamentoDds->data_treinamento->format('d/m/Y') }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td data-label="Status">
                                    @if ($obra->status === 'ativa')
                                        <span class="badge-status badge-success">Ativa</span>
                                    @else
                                        <span class="badge-status badge-warning">Inativa</span>
                                    @endif
                                </td>

                                <td data-label="Ações">
                                    <div class="table-actions">
                                        <a href="{{ route('obras.dds.historico', $obra) }}"
                                           class="btn btn-info-soft btn-icon tip"
                                           data-tip="Histórico DDS"
                                           aria-label="Histórico DDS">
                                            <i class="bi bi-journal-text"></i>
                                            <span class="label-mobile">DDS</span>
                                        </a>

                                        <button type="button"
                                            class="btn btn-dark btn-icon tip"
                                            data-tip="Editar"
                                            aria-label="Editar obra"
                                            data-id="{{ $obra->id }}"
                                            data-nome="{{ $obra->nome }}"
                                            data-endereco="{{ $obra->endereco }}"
                                            data-responsavel="{{ $obra->responsavel }}"
                                            data-data-inicio="{{ $obra->data_inicio }}"
                                            data-status="{{ $obra->status }}"
                                            data-observacoes="{{ $obra->observacoes }}"
                                            onclick="openEditModalFromButton(this)">
                                            <i class="bi bi-pencil-square"></i>
                                            <span class="label-mobile">Editar</span>
                                        </button>

                                        <form method="POST"
                                              action="{{ route('obras.toggle-status', $obra) }}"
                                              onsubmit="return confirm('Tem certeza que deseja {{ $obra->status === 'ativa' ? 'inativar' : 'ativar' }} esta obra?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-warning-soft btn-icon tip"
                                                data-tip="{{ $obra->status === 'ativa' ? 'Inativar' : 'Ativar' }}"
                                                aria-label="{{ $obra->status === 'ativa' ? 'Inativar' : 'Ativar' }}">
                                                <i class="bi bi-arrow-repeat"></i>
                                                <span class="label-mobile">{{ $obra->status === 'ativa' ? 'Inativar' : 'Ativar' }}</span>
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('obras.destroy', $obra) }}"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta obra?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-danger-soft btn-icon tip"
                                                data-tip="Excluir"
                                                aria-label="Excluir">
                                                <i class="bi bi-trash"></i>
                                                <span class="label-mobile">Excluir</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-building"></i>
                                        @if ($filtrosAtivos)
                                            <div class="empty-state__title">Nenhuma obra encontrada</div>
                                            <div>Tente ajustar a busca ou limpar os filtros.</div>
                                        @else
                                            <div class="empty-state__title">Nenhuma obra cadastrada</div>
                                            <div>Clique em “Nova obra” para começar.</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL CRIAR --}}
    <div class="custom-modal" id="createObraModal" role="dialog" aria-modal="true" aria-labelledby="createObraTitle">
        <div class="custom-modal-backdrop" onclick="closeCreateModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="createObraTitle">Nova obra</h3>
                    <p>Preencha os dados da obra para começar a gerenciá-la no sistema.</p>
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

                <form action="{{ route('obras.store') }}" method="POST">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-building"></i> Nome da obra
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-building input-icon"></i>
                                <input
                                    type="text"
                                    name="nome"
                                    class="form-control-custom"
                                    value="{{ old('nome') }}"
                                    placeholder="Ex.: Residencial Aurora"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-person-badge"></i> Técnico de Segurança
                            </label>
                            <select name="responsavel" class="form-control-custom">
                                <option value="">Selecione</option>
                                @foreach ($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id }}" {{ old('responsavel') == $tecnico->id ? 'selected' : '' }}>
                                        {{ $tecnico->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-calendar3"></i> Data de início
                            </label>
                            <input
                                type="date"
                                name="data_inicio"
                                class="form-control-custom"
                                value="{{ old('data_inicio') }}"
                            >
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-geo-alt"></i> Endereço
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-geo-alt input-icon"></i>
                                <input
                                    type="text"
                                    name="endereco"
                                    class="form-control-custom"
                                    value="{{ old('endereco') }}"
                                    placeholder="Rua, número, bairro, cidade"
                                >
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-toggle-on"></i> Status
                            </label>
                            <select name="status" class="form-control-custom" required>
                                <option value="ativa" {{ old('status', 'ativa') === 'ativa' ? 'selected' : '' }}>Ativa</option>
                                <option value="inativa" {{ old('status') === 'inativa' ? 'selected' : '' }}>Inativa</option>
                            </select>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Obras inativas não recebem novos registros operacionais.
                            </div>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-chat-square-text"></i> Observações
                            </label>
                            <textarea
                                name="observacoes"
                                class="form-control-custom"
                                rows="4"
                                placeholder="Anotações internas, contatos, particularidades da obra..."
                            >{{ old('observacoes') }}</textarea>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content:flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeCreateModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Salvar obra
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div class="custom-modal" id="editObraModal" role="dialog" aria-modal="true" aria-labelledby="editObraTitle">
        <div class="custom-modal-backdrop" onclick="closeEditModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="editObraTitle">Editar obra</h3>
                    <p>Atualize os dados da obra selecionada.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="closeEditModal()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                @if ($errors->any() && session('open_edit_modal'))
                    <div class="alert-error-box">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="editObraForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-building"></i> Nome da obra
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-building input-icon"></i>
                                <input type="text" name="nome" id="edit_nome" class="form-control-custom" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-person-badge"></i> Técnico de Segurança
                            </label>
                            <select name="responsavel" id="edit_responsavel" class="form-control-custom">
                                <option value="">Selecione</option>
                                @foreach ($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-calendar3"></i> Data de início
                            </label>
                            <input type="date" name="data_inicio" id="edit_data_inicio" class="form-control-custom">
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-geo-alt"></i> Endereço
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-geo-alt input-icon"></i>
                                <input type="text" name="endereco" id="edit_endereco" class="form-control-custom">
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-toggle-on"></i> Status
                            </label>
                            <select name="status" id="edit_status" class="form-control-custom" required>
                                <option value="ativa">Ativa</option>
                                <option value="inativa">Inativa</option>
                            </select>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-chat-square-text"></i> Observações
                            </label>
                            <textarea name="observacoes" id="edit_observacoes" class="form-control-custom" rows="4"></textarea>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content: flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeEditModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Atualizar obra
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
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

            function openCreateModal() {
                openModal(document.getElementById('createObraModal'));
            }

            function closeCreateModal() {
                closeModal(document.getElementById('createObraModal'));
            }

            function openEditModal(id, nome, endereco, responsavel, dataInicio, status, observacoes) {
                const modal = document.getElementById('editObraModal');
                const form = document.getElementById('editObraForm');

                if (!modal || !form) return;

                form.action = `/gerenciar-obras/${id}`;
                document.getElementById('edit_nome').value = nome ?? '';
                document.getElementById('edit_endereco').value = endereco ?? '';
                document.getElementById('edit_responsavel').value = responsavel ?? '';
                document.getElementById('edit_data_inicio').value = dataInicio ?? '';
                document.getElementById('edit_status').value = status ?? 'ativa';
                document.getElementById('edit_observacoes').value = observacoes ?? '';

                openModal(modal);
            }

            function openEditModalFromButton(button) {
                if (!button) return;

                openEditModal(
                    button.dataset.id,
                    button.dataset.nome,
                    button.dataset.endereco,
                    button.dataset.responsavel,
                    button.dataset.dataInicio,
                    button.dataset.status,
                    button.dataset.observacoes
                );
            }

            function closeEditModal() {
                closeModal(document.getElementById('editObraModal'));
            }

            function initObrasPage() {
                const createModal = document.getElementById('createObraModal');
                const editModal = document.getElementById('editObraModal');

                if (!createModal || !editModal) return;

                const openCreateModalFlag = @json((bool) session('open_create_modal'));
                const obraEdit = @json($obraEditSession);

                if (openCreateModalFlag) {
                    openCreateModal();
                }

                if (obraEdit && obraEdit.id) {
                    openEditModal(
                        obraEdit.id,
                        obraEdit.nome ?? '',
                        obraEdit.endereco ?? '',
                        obraEdit.responsavel ?? '',
                        obraEdit.data_inicio ?? '',
                        obraEdit.status ?? 'ativa',
                        obraEdit.observacoes ?? ''
                    );
                }
            }

            window.openCreateModal = openCreateModal;
            window.closeCreateModal = closeCreateModal;
            window.openEditModal = openEditModal;
            window.openEditModalFromButton = openEditModalFromButton;
            window.closeEditModal = closeEditModal;
            window.initObrasPage = initObrasPage;

            document.addEventListener('DOMContentLoaded', initObrasPage);
            document.addEventListener('page:updated', initObrasPage);
            document.addEventListener('turbo:load', initObrasPage);
            document.addEventListener('livewire:navigated', initObrasPage);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeCreateModal();
                    closeEditModal();
                }
            });
        })();
    </script>
@endsection