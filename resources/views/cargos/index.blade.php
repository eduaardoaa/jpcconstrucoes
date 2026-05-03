@extends('layouts.app')

@section('title', 'Gerenciamento de Cargos')
@section('pageTitle', 'Gerenciamento de Cargos')
@section('pageDescription', 'Gerencie os cargos do sistema.')

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
        --danger-soft: rgba(239,68,68, 0.12);
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

    .btn-mini {
        background: rgba(15, 23, 42, 0.72);
        border: 1px solid var(--border-strong);
        color: var(--text);
        padding: 7px 12px;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 9px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color var(--t-med) var(--ease), border-color var(--t-med) var(--ease), color var(--t-med) var(--ease);
    }

    .btn-mini:hover {
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
        padding: 18px 20px 16px;
        border-bottom: 1px solid var(--border);
        background: rgba(255, 255, 255, 0.01);
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
        background: var(--neutral-2);
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
        min-width: 0;
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

    .avatar-user {
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.22), rgba(96, 165, 250, 0.08));
        color: #93c5fd;
        border-color: rgba(96, 165, 250, 0.25);
    }

    .avatar-worker {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.22), rgba(245, 158, 11, 0.08));
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.25);
    }

    .avatar-both {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.22), rgba(16, 185, 129, 0.08));
        color: #6ee7b7;
        border-color: rgba(16, 185, 129, 0.25);
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

    .badge-blue {
        background: rgba(96, 165, 250, 0.12);
        color: #93c5fd;
        border-color: rgba(96, 165, 250, 0.22);
    }

    .perm-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        max-width: 360px;
    }

    .perm-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        background: rgba(13, 110, 253, 0.10);
        color: #93c5fd;
        border: 1px solid rgba(13, 110, 253, 0.18);
        line-height: 1.2;
    }

    .perm-pill i {
        font-size: 11px;
        opacity: 0.85;
    }

    .text-muted-small {
        font-size: 12.5px;
        color: var(--text-subtle);
        font-style: italic;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

    .perm-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-strong);
        margin-bottom: 4px;
    }

    .perm-count {
        font-size: 11px;
        font-weight: 700;
        color: #93c5fd;
        background: rgba(13, 110, 253, 0.10);
        border: 1px solid rgba(13, 110, 253, 0.18);
        padding: 2px 8px;
        border-radius: 999px;
    }

    .perm-section-hint {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .perm-toolbar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .perm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
        background: #0b1220;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 18px;
        /* Removido max-height para mostrar os grupos inteiros */
    }

    .perm-group-title {
        grid-column: 1 / -1;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--accent);
        letter-spacing: 0.1em;
        margin-top: 18px;
        margin-bottom: 8px;
        padding-bottom: 6px;
        border-bottom: 1px solid var(--accent-soft);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .perm-group-title i {
        font-size: 15px;
        color: var(--accent);
    }

    .perm-group-title:first-child {
        margin-top: 0;
    }


    .perm-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #0f172a;
        border: 1px solid var(--border);
        border-radius: 10px;
        cursor: pointer;
        transition: background-color var(--t-med) var(--ease), border-color var(--t-med) var(--ease), transform var(--t-fast) var(--ease);
        user-select: none;
    }

    .perm-item:hover {
        background: #111827;
        border-color: var(--border-strong);
    }

    .perm-item input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 5px;
        border: 1.5px solid var(--border-strong);
        background: #0b1220;
        cursor: pointer;
        flex-shrink: 0;
        position: relative;
        transition: background-color var(--t-fast) var(--ease), border-color var(--t-fast) var(--ease);
    }

    .perm-item input[type="checkbox"]:checked {
        background: linear-gradient(135deg, #0b5ed7, #0d6efd);
        border-color: var(--accent);
    }

    .perm-item input[type="checkbox"]:checked::after {
        content: "";
        position: absolute;
        left: 5px;
        top: 1px;
        width: 5px;
        height: 10px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .perm-item:has(input:checked) {
        background: rgba(13, 110, 253, 0.06);
        border-color: rgba(13, 110, 253, 0.3);
    }

    .perm-item__label {
        font-size: 13.5px;
        color: var(--text);
        font-weight: 500;
        line-height: 1.3;
    }

    .perm-item:has(input:checked) .perm-item__label {
        color: var(--text-strong);
    }

    .perm-empty {
        text-align: center;
        padding: 30px 16px;
        color: var(--text-muted);
        font-size: 13px;
        grid-column: 1 / -1;
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

        .perm-grid {
            grid-template-columns: 1fr;
            max-height: 260px;
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

        .table td[data-label="Tipo"] {
            position: absolute;
            top: 14px;
            right: 14px;
            width: auto !important;
            padding: 0 !important;
        }

        .table td[data-label="Tipo"]::before {
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

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="page-head__text">
                <h2>Cargos do sistema</h2>
                <p>Cadastre, edite, exclua cargos e defina permissões de acesso aos módulos.</p>
            </div>
        </div>

        <div class="actions-inline">
            <button type="button" class="btn btn-green" onclick="openCreateModal()">
                <i class="bi bi-plus-circle"></i>
                Novo cargo
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
                <div class="card-title">Lista de cargos</div>
                <div class="card-subtitle">Todos os cargos cadastrados no sistema.</div>
            </div>
            <span class="card-count">{{ $cargos->count() }} {{ $cargos->count() === 1 ? 'cargo' : 'cargos' }}</span>
        </div>

        <div class="filters-wrap">
    <form method="GET" action="{{ route('cargos.index') }}" id="formFiltrosCargos">
        <div class="form-grid filtros-grid-cargos">
            <div class="form-group">
                <label class="form-label" for="filtro_busca_cargo">
                    <i class="bi bi-search"></i> Pesquisar cargo
                </label>
                <div class="input-wrap has-icon">
                    <i class="bi bi-search input-icon"></i>
                    <input
                        type="text"
                        name="busca"
                        id="filtro_busca_cargo"
                        class="form-control-custom"
                        placeholder="Digite o nome do cargo"
                        value="{{ request('busca') }}"
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="filtro_tipo_cargo">
                    <i class="bi bi-funnel"></i> Filtrar por tipo
                </label>
                <select name="tipo" id="filtro_tipo_cargo" class="form-control-custom">
                    <option value="">Todos os tipos</option>
                    <option value="usuario" {{ request('tipo') === 'usuario' ? 'selected' : '' }}>Usuário</option>
                    <option value="funcionario" {{ request('tipo') === 'funcionario' ? 'selected' : '' }}>Funcionário</option>
                    <option value="ambos" {{ request('tipo') === 'ambos' ? 'selected' : '' }}>Ambos</option>
                </select>
            </div>

            <div class="filters-actions-cargos">
                <a href="{{ route('cargos.index') }}" class="btn btn-dark">
                    <i class="bi bi-arrow-clockwise"></i>
                    Limpar filtros
                </a>

                <button type="submit" class="btn btn-green">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cargo</th>
                            <th>Tipo</th>
                            <th>Permissões</th>
                            <th style="text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cargos as $cargo)
                            <tr>
                                <td data-label="Nome">
                                    @php
                                        $iniciaisCargo = collect(explode(' ', trim($cargo->nome)))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                            ->implode('');

                                        $avatarClass = match($cargo->tipo) {
                                            'usuario' => 'avatar avatar-user',
                                            'funcionario' => 'avatar avatar-worker',
                                            default => 'avatar avatar-both',
                                        };
                                    @endphp

                                    <div class="user-cell">
                                        <span class="{{ $avatarClass }}">{{ $iniciaisCargo ?: '?' }}</span>
                                        <div>
                                            <div class="user-cell__name">{{ $cargo->nome }}</div>
                                            <div class="user-cell__sub">{{ $cargo->descricao ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Tipo">
                                    @if ($cargo->tipo === 'usuario')
                                        <span class="badge-status badge-blue">Usuário</span>
                                    @elseif ($cargo->tipo === 'funcionario')
                                        <span class="badge-status badge-warning">Funcionário</span>
                                    @else
                                        <span class="badge-status badge-success">Ambos</span>
                                    @endif
                                </td>

                                <td data-label="Permissões">
                                    @if ($cargo->tipo === 'funcionario')
                                        <span class="text-muted-small">
                                            <i class="bi bi-dash-circle"></i> Sem permissões de sistema
                                        </span>
                                    @else
                                        @if ($cargo->permissoes->isEmpty())
                                            <span class="text-muted-small">
                                                <i class="bi bi-dash-circle"></i> Nenhuma permissão
                                            </span>
                                        @else
                                            <div class="perm-list">
                                                @foreach ($cargo->permissoes as $permissao)
                                                    <span class="perm-pill">
                                                        <i class="bi bi-check2"></i>
                                                        {{ $permissao->nome }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <td data-label="Ações">
                                    <div class="table-actions">
                                        <button
                                            type="button"
                                            class="btn btn-dark btn-icon tip"
                                            data-tip="Editar"
                                            aria-label="Editar cargo"
                                            data-id="{{ $cargo->id }}"
                                            data-nome="{{ $cargo->nome }}"
                                            data-descricao="{{ $cargo->descricao }}"
                                            data-tipo="{{ $cargo->tipo }}"
                                            data-permissoes='@json($cargo->permissoes->pluck("id"))'
                                            onclick="openEditModalFromButton(this)"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            <span class="label-mobile">Editar</span>
                                        </button>

                                        <form
                                            method="POST"
                                            action="{{ route('cargos.destroy', $cargo) }}"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este cargo?')"
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
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-shield-slash"></i>
                                        <div class="empty-state__title">Nenhum cargo encontrado</div>
                                        <div>Clique em “Novo cargo” para começar.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>    {{-- MODAL CRIAR --}}
    <div class="custom-modal" id="createCargoModal" role="dialog">
        <div class="custom-modal-backdrop" onclick="closeCreateModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3>Novo cargo</h3>
                    <p>Preencha os dados do cargo.</p>
                </div>

                <button class="custom-modal-close" onclick="closeCreateModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form method="POST" action="{{ route('cargos.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control-custom" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" id="create_tipo" class="form-control-custom" onchange="toggleCreatePermissions()" required>
                                <option value="">Selecione</option>
                                <option value="usuario">Usuário</option>
                                <option value="funcionario">Funcionário</option>
                                <option value="ambos">Ambos</option>
                            </select>
                        </div>

                        <div class="form-group-full">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="descricao" class="form-control-custom">
                        </div>

                        {{-- PERMISSÕES --}}
                        <div class="form-group-full" id="create_permissions_wrapper" style="display:none;">
                            <div class="form-divider"></div>

                            <div class="perm-grid">
                                @foreach ($permissoesAgrupadas as $titulo => $perms)
                                    <div class="perm-group-title">
                                        @if(str_contains($titulo, 'Administrativo')) <i class="bi bi-shield-lock"></i>
                                        @elseif(str_contains($titulo, 'Combustível')) <i class="bi bi-fuel-pump"></i>
                                        @elseif(str_contains($titulo, 'Suprimentos')) <i class="bi bi-box-seam"></i>
                                        @elseif(str_contains($titulo, 'Recrutamento')) <i class="bi bi-person-plus"></i>
                                        @elseif(str_contains($titulo, 'Comunicação')) <i class="bi bi-chat-dots"></i>
                                        @else <i class="bi bi-layers"></i> @endif
                                        {{ $titulo }}
                                    </div>
                                    @foreach ($perms as $permissao)
                                        <label class="perm-item">
                                            <input type="checkbox"
                                                   class="create-permissao-checkbox"
                                                   name="permissoes[]"
                                                   value="{{ $permissao->id }}">
                                            <span>{{ $permissao->nome }}</span>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content:flex-end;">
                            <button type="button" class="btn btn-dark" onclick="closeCreateModal()">Cancelar</button>
                            <button type="submit" class="btn btn-green">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- MODAL EDITAR --}}
    <div class="custom-modal" id="editCargoModal">
        <div class="custom-modal-backdrop" onclick="closeEditModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3>Editar cargo</h3>
                </div>

                <button class="custom-modal-close" onclick="closeEditModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form id="editCargoForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nome</label>
                            <input type="text" id="edit_nome" name="nome" class="form-control-custom">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo</label>
                            <select id="edit_tipo" name="tipo" class="form-control-custom" onchange="toggleEditPermissions()">
                                <option value="usuario">Usuário</option>
                                <option value="funcionario">Funcionário</option>
                                <option value="ambos">Ambos</option>
                            </select>
                        </div>

                        <div class="form-group-full">
                            <label class="form-label">Descrição</label>
                            <input type="text" id="edit_descricao" name="descricao" class="form-control-custom">
                        </div>

                        {{-- PERMISSÕES --}}
                        <div class="form-group-full" id="edit_permissions_wrapper" style="display:none;">
                            <div class="form-divider"></div>

                            <div class="perm-grid">
                                @foreach ($permissoesAgrupadas as $titulo => $perms)
                                    <div class="perm-group-title">
                                        @if(str_contains($titulo, 'Administrativo')) <i class="bi bi-shield-lock"></i>
                                        @elseif(str_contains($titulo, 'Combustível')) <i class="bi bi-fuel-pump"></i>
                                        @elseif(str_contains($titulo, 'Suprimentos')) <i class="bi bi-box-seam"></i>
                                        @elseif(str_contains($titulo, 'Recrutamento')) <i class="bi bi-person-plus"></i>
                                        @elseif(str_contains($titulo, 'Comunicação')) <i class="bi bi-chat-dots"></i>
                                        @else <i class="bi bi-layers"></i> @endif
                                        {{ $titulo }}
                                    </div>
                                    @foreach ($perms as $permissao)
                                        <label class="perm-item">
                                            <input type="checkbox"
                                                   class="edit-permissao-checkbox"
                                                   name="permissoes[]"
                                                   value="{{ $permissao->id }}">
                                            <span>{{ $permissao->nome }}</span>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content:flex-end;">
                            <button type="button" class="btn btn-dark" onclick="closeEditModal()">Cancelar</button>
                            <button type="submit" class="btn btn-green">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function openModal(modal){
            modal.classList.add('is-open');
            document.body.classList.add('modal-open');
        }

        function closeModal(modal){
            modal.classList.remove('is-open');
            document.body.classList.remove('modal-open');
        }

        function openCreateModal(){
            openModal(document.getElementById('createCargoModal'));
        }

        function closeCreateModal(){
            closeModal(document.getElementById('createCargoModal'));
        }

        function openEditModalFromButton(btn){
            let permissoes = JSON.parse(btn.dataset.permissoes || '[]');

            document.getElementById('editCargoForm').action = `/gerenciar-cargos/${btn.dataset.id}`;
            document.getElementById('edit_nome').value = btn.dataset.nome;
            document.getElementById('edit_descricao').value = btn.dataset.descricao;
            document.getElementById('edit_tipo').value = btn.dataset.tipo;

            document.querySelectorAll('.edit-permissao-checkbox').forEach(el => el.checked = false);

            permissoes.forEach(id => {
                const el = document.querySelector(`.edit-permissao-checkbox[value="${id}"]`);
                if (el) el.checked = true;
            });

            toggleEditPermissions();
            openModal(document.getElementById('editCargoModal'));
        }

        function closeEditModal(){
            closeModal(document.getElementById('editCargoModal'));
        }

        function toggleCreatePermissions(){
            const tipo = document.getElementById('create_tipo').value;
            document.getElementById('create_permissions_wrapper').style.display =
                (tipo === 'usuario' || tipo === 'ambos') ? 'block' : 'none';
        }

        function toggleEditPermissions(){
            const tipo = document.getElementById('edit_tipo').value;
            document.getElementById('edit_permissions_wrapper').style.display =
                (tipo === 'usuario' || tipo === 'ambos') ? 'block' : 'none';
        }

        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape'){
                closeCreateModal();
                closeEditModal();
            }
        });
    </script>
@endsection