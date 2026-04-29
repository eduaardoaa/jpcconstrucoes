@extends('layouts.app')

@section('title', 'Instâncias WhatsApp')
@section('pageTitle', 'Instâncias WhatsApp')
@section('pageDescription', 'Gerencie os números conectados à Evolution API.')

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

            --success-soft: rgba(13, 110, 253, 0.12);
            --warning: #f59e0b;
            --warning-soft: rgba(245, 158, 11, 0.12);
            --danger: #ef4444;
            --danger-soft: rgba(239, 68, 68, 0.12);
            --info-soft: rgba(148, 163, 184, 0.12);

            --radius-md: 14px;
            --ease: cubic-bezier(.22, .61, .36, 1);
            --t-fast: 180ms;
            --t-med: 260ms;
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 14px;
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

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-green {
            background: linear-gradient(135deg, #0b5ed7, #0d6efd);
            color: #fff;
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.22);
        }

        .btn-green:hover {
            color: #fff;
            box-shadow: 0 10px 24px rgba(13, 110, 253, 0.32);
        }

        .btn-dark {
            background: rgba(15, 23, 42, 0.72);
            border-color: var(--border-strong);
            color: var(--text);
        }

        .btn-dark:hover {
            background: rgba(30, 41, 59, 0.92);
            color: #fff;
        }

        .btn-danger-soft {
            background: var(--danger-soft);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.20);
        }

        .btn-success-soft {
            background: rgba(13, 110, 253, 0.12);
            color: #93c5fd;
            border-color: rgba(13, 110, 253, 0.22);
        }

        .btn-warning-soft {
            background: var(--warning-soft);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.20);
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
            align-items: flex-start;
        }

        .alert-success-box {
            background: var(--success-soft);
            color: #93c5fd;
            border: 1px solid rgba(13, 110, 253, 0.22);
        }

        .alert-error-box {
            background: var(--danger-soft);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.22);
        }

        .alert-error-box ul {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .filters-card {
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 16px;
            background: transparent;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 240px auto auto;
            gap: 10px;
            align-items: center;
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
            white-space: nowrap;
        }

        .table tbody td {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border-soft);
            color: var(--text);
            vertical-align: middle;
            background: transparent;
        }

        .table tbody tr:hover td {
            background: rgba(255, 255, 255, 0.025);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .instance-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .instance-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.24), rgba(13, 110, 253, 0.08));
            color: #bfdbfe;
            font-weight: 800;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid rgba(13, 110, 253, 0.22);
            text-transform: uppercase;
        }

        .instance-cell__name {
            font-weight: 700;
            color: var(--text-strong);
            line-height: 1.2;
        }

        .instance-cell__sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            color: #cbd5e1;
            font-size: 12.5px;
        }

        .webhook-copy {
            display: flex;
            gap: 8px;
            align-items: center;
            min-width: 360px;
        }

        .webhook-copy input {
            height: 38px;
            font-size: 12px;
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

        .badge-danger {
            background: var(--danger-soft);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.22);
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
            width: min(800px, calc(100% - 24px));
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

        textarea.form-control-custom {
            min-height: 110px;
            resize: vertical;
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

        .field-error {
            color: #fca5a5;
            font-size: 12px;
            margin-top: 6px;
        }

        .modal-open {
            overflow: hidden;
        }

        @media (max-width: 900px) {
            .page-head {
                flex-direction: column;
                align-items: stretch;
            }

            .page-head .actions-inline,
            .page-head .actions-inline .btn {
                width: 100%;
            }

            .filter-form {
                grid-template-columns: 1fr;
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

            .webhook-copy {
                min-width: 0;
                width: 100%;
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
            }

            .table-actions .btn-icon .label-mobile {
                display: inline;
            }

            .tip::after {
                display: none;
            }
        }

        @media (min-width: 901px) {
            .label-mobile {
                display: none;
            }
        }
    </style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon">
                <i class="bi bi-whatsapp"></i>
            </div>
            <div class="page-head__text">
                <h2>Instâncias WhatsApp</h2>
                <p>Cadastre e gerencie os números conectados à Evolution API.</p>
            </div>
        </div>

        <div class="actions-inline">
            <button type="button" class="btn btn-green" onclick="openCreateModal()">
                <i class="bi bi-plus-circle"></i>
                Nova instância
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-box">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Corrija os campos abaixo:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="filters-card">
        <form method="GET" action="{{ route('whatsapp.instancias.index') }}" class="filter-form">
            <input
                type="text"
                name="busca"
                value="{{ $busca }}"
                class="form-control-custom"
                placeholder="Buscar por nome, instância ou URL..."
            >

            <select name="status" class="form-control-custom">
                <option value="">Todos os status</option>
                <option value="ativa" @selected($status === 'ativa')>Ativa</option>
                <option value="inativa" @selected($status === 'inativa')>Inativa</option>
            </select>

            <button class="btn btn-green" type="submit">
                <i class="bi bi-search"></i> Filtrar
            </button>

            <a href="{{ route('whatsapp.instancias.index') }}" class="btn btn-dark">
                <i class="bi bi-x-circle"></i> Limpar
            </a>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Lista de instâncias</div>
                <div class="card-subtitle">Números cadastrados para atendimento via WhatsApp.</div>
            </div>
            <span class="card-count">
                {{ $instancias->count() }} {{ $instancias->count() === 1 ? 'instância' : 'instâncias' }}
            </span>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Instância Evolution</th>
                            <th>Usuários</th>
                            <th>Status</th>
                            <th>Webhook</th>
                            <th style="text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($instancias as $instancia)
                            @php
                                $sigla = collect(explode(' ', $instancia->nome))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');
                            @endphp

                            <tr>
                                <td data-label="Nome">
                                    <div class="instance-cell">
                                        <span class="instance-avatar">{{ $sigla ?: 'W' }}</span>
                                        <div>
                                            <div class="instance-cell__name">{{ $instancia->nome }}</div>
                                            <div class="instance-cell__sub">
                                                {{ $instancia->observacoes ?: 'Sem observações' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Instância Evolution">
                                    <div class="mono">{{ $instancia->instance_name }}</div>
                                    <div class="instance-cell__sub">
                                        {{ $instancia->api_url ?: 'URL não informada' }}
                                    </div>
                                </td>

                                <td data-label="Usuários">
                                    {{ $instancia->usuarios_count }} vinculado(s)
                                </td>

                                <td data-label="Status">
                                    @if ($instancia->status === 'ativa')
                                        <span class="badge-status badge-success">ATIVA</span>
                                    @else
                                        <span class="badge-status badge-danger">INATIVA</span>
                                    @endif
                                </td>

                                <td data-label="Webhook">
                                    <div class="webhook-copy">
                                        <input
                                            type="text"
                                            readonly
                                            class="form-control-custom"
                                            id="webhook-{{ $instancia->id }}"
                                            value="{{ $instancia->webhook_url }}"
                                        >

                                        <button
                                            type="button"
                                            class="btn btn-dark btn-icon tip"
                                            data-tip="Copiar webhook"
                                            onclick="copyWebhook('{{ $instancia->id }}')"
                                        >
                                            <i class="bi bi-clipboard"></i>
                                            <span class="label-mobile">Copiar</span>
                                        </button>
                                    </div>
                                </td>

                                <td data-label="Ações">
                                    <div class="table-actions">
                                        <button
                                            type="button"
                                            class="btn btn-dark btn-icon tip"
                                            data-tip="Editar"
                                            onclick="openEditModal(
                                                '{{ $instancia->id }}',
                                                @js($instancia->nome),
                                                @js($instancia->instance_name),
                                                @js($instancia->api_url),
                                                @js($instancia->status),
                                                @js($instancia->observacoes)
                                            )"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            <span class="label-mobile">Editar</span>
                                        </button>

                                        @if ($instancia->status === 'ativa')
                                            <form action="{{ route('whatsapp.instancias.toggle-status', $instancia) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger-soft btn-icon tip" data-tip="Inativar">
                                                    <i class="bi bi-x-circle"></i>
                                                    <span class="label-mobile">Inativar</span>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('whatsapp.instancias.toggle-status', $instancia) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success-soft btn-icon tip" data-tip="Ativar">
                                                    <i class="bi bi-check-circle"></i>
                                                    <span class="label-mobile">Ativar</span>
                                                </button>
                                            </form>
                                        @endif

                                        <form
                                            action="{{ route('whatsapp.instancias.regenerar-webhook', $instancia) }}"
                                            method="POST"
                                            onsubmit="return confirm('Regenerar o webhook? O link antigo vai parar de funcionar.')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning-soft btn-icon tip" data-tip="Regenerar webhook">
                                                <i class="bi bi-arrow-clockwise"></i>
                                                <span class="label-mobile">Webhook</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-whatsapp"></i>
                                        <div class="empty-state__title">Nenhuma instância cadastrada</div>
                                        <div>Clique em "Nova instância" para começar.</div>
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
    <div class="custom-modal" id="createWhatsappModal" role="dialog" aria-modal="true">
        <div class="custom-modal-backdrop" onclick="closeCreateModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3>Nova instância WhatsApp</h3>
                    <p>Cadastre o número/instância que será conectado na Evolution API.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="closeCreateModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form action="{{ route('whatsapp.instancias.store') }}" method="POST">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-whatsapp"></i> Nome interno *</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-whatsapp input-icon"></i>
                                <input
                                    type="text"
                                    name="nome"
                                    class="form-control-custom"
                                    value="{{ old('nome') }}"
                                    placeholder="Ex: Comercial JPC"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-hash"></i> Instância Evolution *</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-hash input-icon"></i>
                                <input
                                    type="text"
                                    name="instance_name"
                                    class="form-control-custom"
                                    value="{{ old('instance_name') }}"
                                    placeholder="Ex: comercial_jpc"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-link-45deg"></i> URL da Evolution API</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-link-45deg input-icon"></i>
                                <input
                                    type="url"
                                    name="api_url"
                                    class="form-control-custom"
                                    value="{{ old('api_url') }}"
                                    placeholder="Ex: https://evolution.seudominio.com"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-key"></i> API Key</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-key input-icon"></i>
                                <input
                                    type="password"
                                    name="api_key"
                                    class="form-control-custom"
                                    placeholder="Cole a API Key da Evolution"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-toggle-on"></i> Status *</label>
                            <select name="status" class="form-control-custom" required>
                                <option value="ativa" @selected(old('status', 'ativa') === 'ativa')>Ativa</option>
                                <option value="inativa" @selected(old('status') === 'inativa')>Inativa</option>
                            </select>
                        </div>

                        <div class="form-divider"></div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-chat-left-text"></i> Observações</label>
                            <textarea
                                name="observacoes"
                                class="form-control-custom"
                                placeholder="Ex: número usado pelo comercial, vendas, atendimento inicial..."
                            >{{ old('observacoes') }}</textarea>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content:flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeCreateModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Salvar instância
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div class="custom-modal" id="editWhatsappModal" role="dialog" aria-modal="true">
        <div class="custom-modal-backdrop" onclick="closeEditModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3>Editar instância WhatsApp</h3>
                    <p>Atualize os dados da instância selecionada.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="closeEditModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form id="editWhatsappForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-whatsapp"></i> Nome interno *</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-whatsapp input-icon"></i>
                                <input type="text" name="nome" id="edit_nome" class="form-control-custom" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-hash"></i> Instância Evolution *</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-hash input-icon"></i>
                                <input type="text" name="instance_name" id="edit_instance_name" class="form-control-custom" required>
                            </div>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-link-45deg"></i> URL da Evolution API</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-link-45deg input-icon"></i>
                                <input type="url" name="api_url" id="edit_api_url" class="form-control-custom">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-key"></i> Nova API Key</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-key input-icon"></i>
                                <input
                                    type="password"
                                    name="api_key"
                                    class="form-control-custom"
                                    placeholder="Deixe vazio para manter a atual"
                                >
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i> Preencha somente se quiser trocar a chave.
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-toggle-on"></i> Status *</label>
                            <select name="status" id="edit_status" class="form-control-custom" required>
                                <option value="ativa">Ativa</option>
                                <option value="inativa">Inativa</option>
                            </select>
                        </div>

                        <div class="form-divider"></div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-chat-left-text"></i> Observações</label>
                            <textarea name="observacoes" id="edit_observacoes" class="form-control-custom"></textarea>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content:flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeEditModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Atualizar instância
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
                openModal(document.getElementById('createWhatsappModal'));
            }

            function closeCreateModal() {
                closeModal(document.getElementById('createWhatsappModal'));
            }

            function openEditModal(id, nome, instanceName, apiUrl, status, observacoes) {
                const modal = document.getElementById('editWhatsappModal');
                const form = document.getElementById('editWhatsappForm');

                if (!modal || !form) return;

                form.action = `/whatsapp/instancias/${id}`;

                document.getElementById('edit_nome').value = nome ?? '';
                document.getElementById('edit_instance_name').value = instanceName ?? '';
                document.getElementById('edit_api_url').value = apiUrl ?? '';
                document.getElementById('edit_status').value = status ?? 'ativa';
                document.getElementById('edit_observacoes').value = observacoes ?? '';

                openModal(modal);
            }

            function closeEditModal() {
                closeModal(document.getElementById('editWhatsappModal'));
            }

            async function copyWebhook(id) {
                const input = document.getElementById('webhook-' + id);
                if (!input) return;

                try {
                    await navigator.clipboard.writeText(input.value);
                    input.focus();
                    input.select();
                } catch (e) {
                    input.focus();
                    input.select();
                    document.execCommand('copy');
                }
            }

            window.openCreateModal = openCreateModal;
            window.closeCreateModal = closeCreateModal;
            window.openEditModal = openEditModal;
            window.closeEditModal = closeEditModal;
            window.copyWebhook = copyWebhook;

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeCreateModal();
                    closeEditModal();
                }
            });

            @if(session('abrir_modal_criar_instancia'))
                document.addEventListener('DOMContentLoaded', function () {
                    openCreateModal();
                });
            @endif

            @if(session('abrir_modal_editar_instancia'))
                document.addEventListener('DOMContentLoaded', function () {
                    const id = @json(session('abrir_modal_editar_instancia'));
                    const btn = document.querySelector(`[onclick*="openEditModal('${id}'"]`);
                    if (btn) btn.click();
                });
            @endif
        })();
    </script>
@endsection