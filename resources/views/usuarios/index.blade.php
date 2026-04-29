@extends('layouts.app')
@section('title', 'Gerenciamento de Usuários')
@section('pageTitle', 'Gerenciamento de Usuários')
@section('pageDescription', 'Gerencie os acessos ao sistema.')
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
        --success: #0d6efd;
        --success-soft: rgba(13, 110, 253, 0.12);
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
        color: #93c5fd;
        border: 1px solid rgba(13, 110, 253, 0.22);
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
        grid-template-columns: minmax(0, 1.4fr) 220px 220px auto;
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
        background: rgba(13, 110, 253, 0.12);
        color: #93c5fd;
        border-color: rgba(13, 110, 253, 0.22);
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
    .whatsapp-badges {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        max-width: 260px;
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
    select.form-control-custom {
        padding-right: 40px;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
        background-position: right 14px center;
        background-repeat: no-repeat;
    }
    select[multiple].form-control-custom {
        min-height: 128px;
        padding-right: 14px;
        background-image: none;
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
        .card-header {
            padding: 14px 16px;
            flex-wrap: wrap;
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
        .table-actions form button {
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
        $usuarioEditSession = session('open_edit_modal')
            ? $usuarios->firstWhere('id', session('open_edit_modal'))
            : null;
        $filtrosAtivos = filled($busca ?? null) || filled($cargoId ?? null) || in_array(($status ?? null), ['ativo', 'inativo']);
    @endphp
    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-people-fill"></i></div>
            <div class="page-head__text">
                <h2>Usuários do sistema</h2>
                <p>Cadastre, edite, ative, inative, redefina senha e exclua usuários.</p>
            </div>
        </div>
        <div class="actions-inline">
            <button type="button" class="btn btn-green" onclick="openCreateModal()">
                <i class="bi bi-person-plus"></i>
                Novo usuário
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
                <div class="card-title">Lista de usuários</div>
                <div class="card-subtitle">Todos os acessos cadastrados no sistema.</div>
            </div>
            <span class="card-count">{{ $usuarios->count() }} {{ $usuarios->count() === 1 ? 'usuário' : 'usuários' }}</span>
        </div>
        <div class="filters-wrap">
            <form method="GET" action="{{ route('usuarios.index') }}" class="filters-form">
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
                            value="{{ $busca ?? '' }}"
                            placeholder="Buscar por nome, CPF, e-mail ou telefone..."
                        >
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-briefcase"></i> Cargo
                    </label>
                    <select name="cargo_id" class="form-control-custom">
                        <option value="">Todos</option>
                        @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ (string) ($cargoId ?? '') === (string) $cargo->id ? 'selected' : '' }}>
                                {{ $cargo->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-funnel"></i> Status
                    </label>
                    <select name="status" class="form-control-custom">
                        <option value="">Todos</option>
                        <option value="ativo" {{ ($status ?? '') === 'ativo' ? 'selected' : '' }}>Ativos</option>
                        <option value="inativo" {{ ($status ?? '') === 'inativo' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-green">
                        <i class="bi bi-search"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-dark">
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
                            <th>Usuário</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Cargo</th>
                            <th>Veículo</th>
                            <th>WhatsApp</th>
                            <th>Status</th>
                            <th>1º acesso</th>
                            <th style="text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            @php
                                $iniciais = collect(explode(' ', trim($usuario->name)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');
                            @endphp
                            <tr>
                                <td data-label="Nome">
                                    <div class="user-cell">
                                        <span class="avatar">{{ $iniciais ?: '?' }}</span>
                                        <div>
                                            <div class="user-cell__name">{{ $usuario->name }}</div>
                                            <div class="user-cell__sub">{{ $usuario->email ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="CPF">{{ $usuario->cpf }}</td>
                                <td data-label="Telefone">{{ $usuario->telefone ?: '—' }}</td>
                                <td data-label="Cargo">{{ $usuario->cargo->nome ?? '—' }}</td>
                                <td data-label="Veículo">
                                    @if ($usuario->pode_ter_veiculo)
                                        {{ $usuario->veiculo?->placa ? $usuario->veiculo->placa . ' - ' . $usuario->veiculo->marca . ' ' . $usuario->veiculo->modelo : 'SEM VEICULO' }}
                                    @else
                                        NÃO PERMITIDO
                                    @endif
                                </td>
                                <td data-label="WhatsApp">
                                    @if ($usuario->isAdmin())
                                        <span class="badge-status badge-success">PERMITIDO</span>
                                        <div class="user-cell__sub">Todas as instâncias</div>
                                    @elseif ($usuario->whatsappInstancias->count())
                                        <div class="whatsapp-badges">
                                            @foreach ($usuario->whatsappInstancias as $instancia)
                                                <span class="badge-status badge-info">{{ $instancia->nome }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge-status badge-warning">NÃO PERMITIDO</span>
                                    @endif
                                </td>
                                <td data-label="Status">
                                    @if ($usuario->status === 'ativo')
                                        <span class="badge-status badge-success">Ativo</span>
                                    @else
                                        <span class="badge-status badge-warning">Inativo</span>
                                    @endif
                                </td>
                                <td data-label="1º acesso">
                                    @if ($usuario->primeiro_acesso)
                                        <span class="badge-status badge-info">Pendente</span>
                                    @else
                                        <span class="badge-status badge-success">Concluído</span>
                                    @endif
                                </td>
                                <td data-label="Ações">
                                    <div class="table-actions">
                                        <button
                                            type="button"
                                            class="btn btn-dark btn-icon tip"
                                            data-tip="Editar"
                                            aria-label="Editar usuário"
                                            data-id="{{ $usuario->id }}"
                                            data-name="{{ $usuario->name }}"
                                            data-cpf="{{ $usuario->cpf }}"
                                            data-telefone="{{ $usuario->telefone }}"
                                            data-email="{{ $usuario->email }}"
                                            data-cargo-id="{{ $usuario->cargo_id }}"
                                            data-status="{{ $usuario->status }}"
                                            data-pode-ter-veiculo="{{ $usuario->pode_ter_veiculo ? 1 : 0 }}"
                                            data-veiculo-id="{{ $usuario->veiculo_id ?? '' }}"
                                            data-whatsapp-ids='@json($usuario->whatsappInstancias->pluck("id")->map(fn($id) => (string) $id)->values())'
                                            onclick="openEditModalFromButton(this)"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            <span class="label-mobile">Editar</span>
                                        </button>
                                        <form
                                            method="POST"
                                            action="{{ route('usuarios.toggle-status', $usuario) }}"
                                            onsubmit="return confirm('Tem certeza que deseja {{ $usuario->status === 'ativo' ? 'inativar' : 'ativar' }} este usuário?')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="btn btn-warning-soft btn-icon tip"
                                                data-tip="{{ $usuario->status === 'ativo' ? 'Inativar' : 'Ativar' }}"
                                                aria-label="{{ $usuario->status === 'ativo' ? 'Inativar' : 'Ativar' }}"
                                            >
                                                <i class="bi bi-arrow-repeat"></i>
                                                <span class="label-mobile">{{ $usuario->status === 'ativo' ? 'Inativar' : 'Ativar' }}</span>
                                            </button>
                                        </form>
                                        <form
                                            method="POST"
                                            action="{{ route('usuarios.reset-password', $usuario) }}"
                                            onsubmit="return confirm('Resetar a senha deste usuário para 12345?')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="btn btn-dark btn-icon tip"
                                                data-tip="Resetar senha"
                                                aria-label="Resetar senha"
                                            >
                                                <i class="bi bi-key"></i>
                                                <span class="label-mobile">Resetar senha</span>
                                            </button>
                                        </form>
                                        <form
                                            method="POST"
                                            action="{{ route('usuarios.destroy', $usuario) }}"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-danger-soft btn-icon tip"
                                                data-tip="Excluir"
                                                aria-label="Excluir"
                                            >
                                                <i class="bi bi-trash"></i>
                                                <span class="label-mobile">Excluir</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        @if ($filtrosAtivos)
                                            <div class="empty-state__title">Nenhum usuário encontrado</div>
                                            <div>Tente ajustar a busca ou limpar os filtros.</div>
                                        @else
                                            <div class="empty-state__title">Nenhum usuário cadastrado</div>
                                            <div>Clique em "Novo usuário" para começar.</div>
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
    <div class="custom-modal" id="createUserModal" role="dialog" aria-modal="true" aria-labelledby="createUserTitle">
        <div class="custom-modal-backdrop" onclick="closeCreateModal()"></div>
        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="createUserTitle">Novo usuário</h3>
                    <p>Preencha os dados para cadastrar um novo acesso no sistema.</p>
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
                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-person"></i> Nome completo
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-person input-icon"></i>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control-custom"
                                    value="{{ old('name') }}"
                                    placeholder="Digite o nome completo"
                                    required
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-card-text"></i> CPF
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-card-text input-icon"></i>
                                <input
                                    type="text"
                                    name="cpf"
                                    id="create_cpf"
                                    class="form-control-custom"
                                    value="{{ old('cpf') }}"
                                    maxlength="14"
                                    inputmode="numeric"
                                    placeholder="000.000.000-00"
                                    required
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-telephone"></i> Telefone
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-telephone input-icon"></i>
                                <input
                                    type="text"
                                    name="telefone"
                                    id="create_telefone"
                                    class="form-control-custom"
                                    value="{{ old('telefone') }}"
                                    maxlength="15"
                                    inputmode="numeric"
                                    placeholder="(00) 00000-0000"
                                    required
                                >
                            </div>
                        </div>
                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-envelope"></i> E-mail
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-envelope input-icon"></i>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control-custom"
                                    value="{{ old('email') }}"
                                    placeholder="usuario@empresa.com"
                                    required
                                >
                            </div>
                        </div>
                        <div class="form-divider"></div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-briefcase"></i> Cargo
                            </label>
                            <select name="cargo_id" id="create_cargo_id" class="form-control-custom" required onchange="toggleCreateWhatsappByCargo()">
                                <option value="">Selecione</option>
                                @foreach ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}" {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                        {{ $cargo->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-toggle-on"></i> Status
                            </label>
                            <select name="status" class="form-control-custom" required>
                                <option value="ativo" {{ old('status', 'ativo') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-car-front"></i> Pode ter veículo?
                            </label>
                            <select
                                name="pode_ter_veiculo"
                                id="create_pode_ter_veiculo"
                                class="form-control-custom"
                                required
                                onchange="toggleCreateVeiculoField()"
                            >
                                <option value="0" {{ old('pode_ter_veiculo', '0') == '0' ? 'selected' : '' }}>Não</option>
                                <option value="1" {{ old('pode_ter_veiculo') == '1' ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>
                        <div
                            class="form-group form-group-full"
                            id="create_veiculo_group"
                            style="{{ old('pode_ter_veiculo') == '1' ? '' : 'display:none;' }}"
                        >
                            <label class="form-label">
                                <i class="bi bi-truck"></i> Veículo vinculado
                            </label>
                            <select name="veiculo_id" id="create_veiculo_id" class="form-control-custom">
                                <option value="">Selecione um veículo</option>
                                @foreach ($veiculos as $veiculo)
                                    <option value="{{ $veiculo->id }}" {{ old('veiculo_id') == $veiculo->id ? 'selected' : '' }}>
                                        {{ $veiculo->placa }} - {{ $veiculo->marca }} {{ $veiculo->modelo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-divider"></div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-whatsapp"></i> Pode acessar WhatsApp?
                            </label>
                            <select
                                name="pode_acessar_whatsapp"
                                id="create_pode_acessar_whatsapp"
                                class="form-control-custom"
                                required
                                onchange="toggleCreateWhatsappField()"
                            >
                                <option value="0" {{ old('pode_acessar_whatsapp', '0') == '0' ? 'selected' : '' }}>Não</option>
                                <option value="1" {{ old('pode_acessar_whatsapp') == '1' ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>
                        <div
                            class="form-group form-group-full"
                            id="create_whatsapp_group"
                            style="{{ old('pode_acessar_whatsapp') == '1' ? '' : 'display:none;' }}"
                        >
                            <label class="form-label">
                                <i class="bi bi-phone"></i> Instâncias liberadas
                            </label>
                            <select name="whatsapp_instancias[]" id="create_whatsapp_instancias" class="form-control-custom">
                                <option value="">Selecione uma instância</option>
                                @foreach ($whatsappInstancias as $instancia)
                                    <option value="{{ $instancia->id }}" @selected(collect(old('whatsapp_instancias', []))->contains($instancia->id))>
                                        {{ $instancia->nome }} - {{ $instancia->instance_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Segure Command no Mac ou Ctrl no Windows para selecionar mais de uma instância.
                            </div>
                        </div>
                        <div class="form-group-full">
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                A senha inicial poderá ser redefinida depois, se necessário.
                            </div>
                        </div>
                        <div class="form-group-full actions-inline" style="justify-content:flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeCreateModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Salvar usuário
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL EDITAR --}}
    <div class="custom-modal" id="editUserModal" role="dialog" aria-modal="true" aria-labelledby="editUserTitle">
        <div class="custom-modal-backdrop" onclick="closeEditModal()"></div>
        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="editUserTitle">Editar usuário</h3>
                    <p>Atualize os dados do usuário selecionado.</p>
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
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-person"></i> Nome completo
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text" name="name" id="edit_name" class="form-control-custom" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-card-text"></i> CPF
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-card-text input-icon"></i>
                                <input
                                    type="text"
                                    name="cpf"
                                    id="edit_cpf"
                                    class="form-control-custom"
                                    maxlength="14"
                                    inputmode="numeric"
                                    required
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-telephone"></i> Telefone
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-telephone input-icon"></i>
                                <input
                                    type="text"
                                    name="telefone"
                                    id="edit_telefone"
                                    class="form-control-custom"
                                    maxlength="15"
                                    inputmode="numeric"
                                    required
                                >
                            </div>
                        </div>
                        <div class="form-group form-group-full">
                            <label class="form-label">
                                <i class="bi bi-envelope"></i> E-mail
                            </label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-envelope input-icon"></i>
                                <input type="email" name="email" id="edit_email" class="form-control-custom" required>
                            </div>
                        </div>
                        <div class="form-divider"></div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-briefcase"></i> Cargo
                            </label>
                            <select name="cargo_id" id="edit_cargo_id" class="form-control-custom" required onchange="toggleEditWhatsappByCargo()">
                                <option value="">Selecione</option>
                                @foreach ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-toggle-on"></i> Status
                            </label>
                            <select name="status" id="edit_status" class="form-control-custom" required>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-car-front"></i> Pode ter veículo?
                            </label>
                            <select
                                name="pode_ter_veiculo"
                                id="edit_pode_ter_veiculo"
                                class="form-control-custom"
                                required
                                onchange="toggleEditVeiculoField()"
                            >
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <div class="form-group form-group-full" id="edit_veiculo_group" style="display:none;">
                            <label class="form-label">
                                <i class="bi bi-truck"></i> Veículo vinculado
                            </label>
                            <select name="veiculo_id" id="edit_veiculo_id" class="form-control-custom">
                                <option value="">Selecione um veículo</option>
                                @foreach ($veiculos as $veiculo)
                                    <option value="{{ $veiculo->id }}">
                                        {{ $veiculo->placa }} - {{ $veiculo->marca }} {{ $veiculo->modelo }}
                                    </option>
                                @endforeach
                                @foreach ($usuarios as $u)
                                    @if ($u->veiculo && !$veiculos->contains('id', $u->veiculo->id))
                                        <option value="{{ $u->veiculo->id }}">
                                            {{ $u->veiculo->placa }} - {{ $u->veiculo->marca }} {{ $u->veiculo->modelo }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-divider"></div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-whatsapp"></i> Pode acessar WhatsApp?
                            </label>
                            <select
                                name="pode_acessar_whatsapp"
                                id="edit_pode_acessar_whatsapp"
                                class="form-control-custom"
                                required
                                onchange="toggleEditWhatsappField()"
                            >
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <div class="form-group form-group-full" id="edit_whatsapp_group" style="display:none;">
                            <label class="form-label">
                                <i class="bi bi-phone"></i> Instâncias liberadas
                            </label>
                            <select name="whatsapp_instancias[]" id="edit_whatsapp_instancias" class="form-control-custom">
                                <option value="">Selecione uma instância</option>
                                @foreach ($whatsappInstancias as $instancia)
                                    <option value="{{ $instancia->id }}">
                                        {{ $instancia->nome }} - {{ $instancia->instance_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Selecione os números que esse usuário poderá atender.
                            </div>
                        </div>
                        <div class="form-group-full actions-inline" style="justify-content: flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeEditModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Atualizar usuário
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
            function formatarCpf(value) {
                value = String(value || '').replace(/\D/g, '').slice(0, 11);
                if (value.length > 3) {
                    value = value.replace(/^(\d{3})(\d)/, '$1.$2');
                }
                if (value.length > 6) {
                    value = value.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
                }
                if (value.length > 9) {
                    value = value.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
                }
                return value;
            }
            function formatarTelefone(value) {
                value = String(value || '').replace(/\D/g, '').slice(0, 11);
                if (value.length === 0) return '';
                if (value.length <= 10) {
                    value = value.replace(/^(\d{0,2})/, '($1');
                    value = value.replace(/^\((\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
                } else {
                    value = value.replace(/^(\d{0,2})/, '($1');
                    value = value.replace(/^\((\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
                }
                return value;
            }
            function aplicarMascara(input, formatter) {
                if (!input || input.dataset.maskApplied === 'true') return;
                input.value = formatter(input.value);
                input.addEventListener('input', function (e) {
                    e.target.value = formatter(e.target.value);
                });
                input.addEventListener('keypress', function (e) {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault();
                    }
                });
                input.dataset.maskApplied = 'true';
            }
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
            function aplicarMascarasUsuarios() {
                aplicarMascara(document.getElementById('create_cpf'), formatarCpf);
                aplicarMascara(document.getElementById('create_telefone'), formatarTelefone);
                aplicarMascara(document.getElementById('edit_cpf'), formatarCpf);
                aplicarMascara(document.getElementById('edit_telefone'), formatarTelefone);
            }
            function openCreateModal() {
                aplicarMascarasUsuarios();
                toggleCreateVeiculoField();
                toggleCreateWhatsappByCargo();
                openModal(document.getElementById('createUserModal'));
            }
            function closeCreateModal() {
                closeModal(document.getElementById('createUserModal'));
            }
            function openEditModal(id, name, cpf, telefone, email, cargoId, status, podeTerVeiculo, veiculoId, whatsappIds) {
                whatsappIds = whatsappIds || [];
                const modal = document.getElementById('editUserModal');
                const form = document.getElementById('editUserForm');
                if (!modal || !form) return;
                form.action = `/gerenciar-usuarios/${id}`;
                document.getElementById('edit_name').value = name ?? '';
                document.getElementById('edit_cpf').value = formatarCpf(cpf ?? '');
                document.getElementById('edit_telefone').value = formatarTelefone(telefone ?? '');
                document.getElementById('edit_email').value = email ?? '';
                document.getElementById('edit_cargo_id').value = cargoId ?? '';
                document.getElementById('edit_status').value = status ?? 'ativo';
                document.getElementById('edit_pode_ter_veiculo').value = String(podeTerVeiculo ?? '0');
                document.getElementById('edit_veiculo_id').value = veiculoId ?? '';
                toggleEditVeiculoField();
                const whatsappIdsNormalizados = Array.isArray(whatsappIds)
                    ? whatsappIds.map(String)
                    : [];
                const podeWhatsapp = whatsappIdsNormalizados.length > 0;
                document.getElementById('edit_pode_acessar_whatsapp').value = podeWhatsapp ? '1' : '0';
                const selectWhatsapp = document.getElementById('edit_whatsapp_instancias');
                if (selectWhatsapp) {
                    Array.from(selectWhatsapp.options).forEach(option => {
                        option.selected = whatsappIdsNormalizados.includes(String(option.value));
                    });
                }
                toggleEditWhatsappByCargo();
                aplicarMascarasUsuarios();
                openModal(modal);
            }
            function openEditModalFromButton(button) {
                if (!button) return;
                openEditModal(
                    button.dataset.id,
                    button.dataset.name,
                    button.dataset.cpf,
                    button.dataset.telefone,
                    button.dataset.email,
                    button.dataset.cargoId,
                    button.dataset.status,
                    button.dataset.podeTerVeiculo,
                    button.dataset.veiculoId,
                    JSON.parse(button.dataset.whatsappIds || '[]')
                );
            }
            function closeEditModal() {
                closeModal(document.getElementById('editUserModal'));
            }
            function toggleCreateVeiculoField() {
                const pode = document.getElementById('create_pode_ter_veiculo')?.value === '1';
                const grupo = document.getElementById('create_veiculo_group');
                const select = document.getElementById('create_veiculo_id');
                if (grupo) {
                    grupo.style.display = pode ? 'block' : 'none';
                }
                if (!pode && select) {
                    select.value = '';
                }
            }
            function toggleEditVeiculoField() {
                const pode = document.getElementById('edit_pode_ter_veiculo')?.value === '1';
                const grupo = document.getElementById('edit_veiculo_group');
                const select = document.getElementById('edit_veiculo_id');
                if (grupo) {
                    grupo.style.display = pode ? 'block' : 'none';
                }
                if (!pode && select) {
                    select.value = '';
                }
            }
            function toggleCreateWhatsappField() {
                const pode = document.getElementById('create_pode_acessar_whatsapp')?.value === '1';
                const grupo = document.getElementById('create_whatsapp_group');
                const select = document.getElementById('create_whatsapp_instancias');
                if (grupo) {
                    grupo.style.display = pode ? 'block' : 'none';
                }
                if (!pode && select) {
                    Array.from(select.options).forEach(option => option.selected = false);
                }
            }
            function toggleEditWhatsappField() {
                const pode = document.getElementById('edit_pode_acessar_whatsapp')?.value === '1';
                const grupo = document.getElementById('edit_whatsapp_group');
                const select = document.getElementById('edit_whatsapp_instancias');
                if (grupo) {
                    grupo.style.display = pode ? 'block' : 'none';
                }
                if (!pode && select) {
                    Array.from(select.options).forEach(option => option.selected = false);
                }
            }
            function cargoSelecionadoEhAdmin(selectId) {
                const select = document.getElementById(selectId);
                if (!select) return false;
                const option = select.options[select.selectedIndex];
                const texto = String(option?.text || '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .trim();
                return texto === 'administrador';
            }
            function toggleCreateWhatsappByCargo() {
                const isAdmin = cargoSelecionadoEhAdmin('create_cargo_id');
                const selectPode = document.getElementById('create_pode_acessar_whatsapp');
                const grupoPode = selectPode?.closest('.form-group');
                const grupoInstancias = document.getElementById('create_whatsapp_group');
                if (isAdmin) {
                    if (selectPode) selectPode.value = '0';
                    if (grupoPode) grupoPode.style.display = 'none';
                    if (grupoInstancias) grupoInstancias.style.display = 'none';
                    return;
                }
                if (grupoPode) grupoPode.style.display = 'block';
                toggleCreateWhatsappField();
            }
            function toggleEditWhatsappByCargo() {
                const isAdmin = cargoSelecionadoEhAdmin('edit_cargo_id');
                const selectPode = document.getElementById('edit_pode_acessar_whatsapp');
                const grupoPode = selectPode?.closest('.form-group');
                const grupoInstancias = document.getElementById('edit_whatsapp_group');
                if (isAdmin) {
                    if (selectPode) selectPode.value = '0';
                    if (grupoPode) grupoPode.style.display = 'none';
                    if (grupoInstancias) grupoInstancias.style.display = 'none';
                    return;
                }
                if (grupoPode) grupoPode.style.display = 'block';
                toggleEditWhatsappField();
            }
            function initUsuariosPage() {
                const createModal = document.getElementById('createUserModal');
                const editModal = document.getElementById('editUserModal');
                if (!createModal || !editModal) return;
                aplicarMascarasUsuarios();
                toggleCreateVeiculoField();
                toggleEditVeiculoField();
                toggleCreateWhatsappByCargo();
                toggleEditWhatsappByCargo();
                @if (session('open_create_modal'))
                    openCreateModal();
                @endif
                @if (session('open_edit_modal'))
                    @if ($usuarioEditSession)
                        @php
                            $usuarioEditWhatsappIds = $usuarioEditSession->whatsappInstancias
                                ->pluck('id')
                                ->map(fn ($id) => (string) $id)
                                ->values();
                        @endphp
                        openEditModal(
                            '{{ $usuarioEditSession->id }}',
                            @json($usuarioEditSession->name),
                            @json($usuarioEditSession->cpf),
                            @json($usuarioEditSession->telefone),
                            @json($usuarioEditSession->email),
                            @json($usuarioEditSession->cargo_id),
                            @json($usuarioEditSession->status),
                            @json($usuarioEditSession->pode_ter_veiculo ? 1 : 0),
                            @json($usuarioEditSession->veiculo_id),
                            @json($usuarioEditWhatsappIds)
                        );
                    @endif
                @endif
            }
            window.openCreateModal = openCreateModal;
            window.toggleCreateWhatsappByCargo = toggleCreateWhatsappByCargo;
            window.toggleEditWhatsappByCargo = toggleEditWhatsappByCargo;
            window.closeCreateModal = closeCreateModal;
            window.openEditModal = openEditModal;
            window.openEditModalFromButton = openEditModalFromButton;
            window.closeEditModal = closeEditModal;
            window.toggleCreateVeiculoField = toggleCreateVeiculoField;
            window.toggleEditVeiculoField = toggleEditVeiculoField;
            window.toggleCreateWhatsappField = toggleCreateWhatsappField;
            window.toggleEditWhatsappField = toggleEditWhatsappField;
            window.initUsuariosPage = initUsuariosPage;
            document.addEventListener('DOMContentLoaded', initUsuariosPage);
            document.addEventListener('page:updated', initUsuariosPage);
            document.addEventListener('turbo:load', initUsuariosPage);
            document.addEventListener('livewire:navigated', initUsuariosPage);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeCreateModal();
                    closeEditModal();
                }
            });
        })();
    </script>
@endsection