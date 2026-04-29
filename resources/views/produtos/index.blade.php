@extends('layouts.app')

@section('title', 'Gerenciamento de Produtos')
@section('pageTitle', 'Gerenciamento de Produtos')
@section('pageDescription', 'Gerencie os produtos cadastrados no sistema.')

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

    .btn-primary-soft {
        background: var(--accent-soft);
        color: #93c5fd;
        border-color: rgba(13, 110, 253, 0.25);
    }

    .btn-primary-soft:hover {
        background: rgba(13, 110, 253, 0.18);
        border-color: rgba(13, 110, 253, 0.4);
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
        grid-template-columns: minmax(0, 1.5fr) 220px auto;
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

    .product-cell {
        display: flex;
        align-items: flex-start;
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

    .product-cell__name {
        font-weight: 600;
        color: var(--text-strong);
        line-height: 1.2;
    }

    .product-cell__sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .product-cell__meta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 11.5px;
        color: var(--text-muted);
        padding: 3px 8px;
        background: rgba(148, 163, 184, 0.08);
        border: 1px solid var(--border-soft);
        border-radius: 999px;
    }

    .price-value {
        font-weight: 700;
        color: var(--text-strong);
    }

    .days-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #cbd5e1;
        padding: 4px 10px;
        background: rgba(148, 163, 184, 0.08);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 999px;
    }

    .text-muted-small {
        font-size: 12px;
        color: var(--text-muted);
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

    .variacoes-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        max-width: 360px;
    }

    .variacao-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        background: rgba(13, 110, 253, 0.10);
        color: #93c5fd;
        border: 1px solid rgba(13, 110, 253, 0.20);
        line-height: 1.2;
    }

    .variacao-pill--inactive {
        background: rgba(245, 158, 11, 0.12);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.22);
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
        width: min(780px, calc(100% - 24px));
        margin: auto;
        top: 50%;
        transform: translateY(-50%);
        max-height: 92vh;
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

    textarea.form-control-custom {
        min-height: 110px;
        resize: vertical;
        padding-top: 14px;
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

    .toggle-line {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 48px;
        padding: 10px 14px;
        border-radius: 11px;
        border: 1px solid var(--border-strong);
        background: #0b1220;
        cursor: pointer;
        user-select: none;
        transition: border-color var(--t-med) var(--ease), background-color var(--t-med) var(--ease);
    }

    .toggle-line:hover { border-color: rgba(148, 163, 184, 0.34); }

    .toggle-line:has(input:checked) {
        border-color: rgba(13, 110, 253, 0.4);
        background: rgba(13, 110, 253, 0.06);
    }

    .toggle-line input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 40px;
        height: 22px;
        border-radius: 999px;
        background: var(--border-strong);
        position: relative;
        cursor: pointer;
        flex-shrink: 0;
        transition: background-color var(--t-med) var(--ease);
    }

    .toggle-line input[type="checkbox"]::after {
        content: "";
        position: absolute;
        left: 3px;
        top: 3px;
        width: 16px;
        height: 16px;
        background: #fff;
        border-radius: 50%;
        transition: transform var(--t-med) var(--ease);
        box-shadow: 0 1px 3px rgba(0,0,0,.35);
    }

    .toggle-line input[type="checkbox"]:checked {
        background: linear-gradient(135deg, #0b5ed7, #0d6efd);
    }

    .toggle-line input[type="checkbox"]:checked::after {
        transform: translateX(18px);
    }

    .toggle-line__label {
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.25;
    }

    .toggle-line__label strong {
        font-size: 13.5px;
        color: var(--text-strong);
        font-weight: 600;
    }

    .toggle-line__label small {
        font-size: 11.5px;
        color: var(--text-muted);
    }

    .variacoes-section {
        background: #0b1220;
        border: 1px dashed var(--border-strong);
        border-radius: 14px;
        padding: 16px;
    }

    .variacoes-section__title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-strong);
    }

    .variacoes-section__count {
        font-size: 11px;
        font-weight: 700;
        color: #93c5fd;
        background: rgba(13, 110, 253, 0.10);
        border: 1px solid rgba(13, 110, 253, 0.25);
        padding: 2px 8px;
        border-radius: 999px;
    }

    .variacoes-section__hint {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .variacoes-lista {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .variacao-item {
        background: #0f172a;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px;
    }

    .variacao-item__head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .variacao-item__number {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-strong);
    }

    .variacao-item__badge {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: rgba(13, 110, 253, 0.10);
        color: #93c5fd;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        border: 1px solid rgba(13, 110, 253, 0.25);
    }

    .btn-mini {
        background: rgba(15, 23, 42, 0.6);
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
    }

    .btn-mini:hover {
        background: #162036;
        border-color: #475569;
    }

    .btn-mini.btn-mini--danger {
        background: var(--danger-soft);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.22);
    }

    .btn-mini.btn-mini--danger:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.4);
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
        $produtoEditSession = session('open_edit_modal')
            ? $produtos->firstWhere('id', session('open_edit_modal'))
            : null;

        $filtrosAtivos = filled($busca ?? null) || in_array(($status ?? null), ['ativo', 'inativo']);
    @endphp

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="page-head__text">
                <h2>Produtos do sistema</h2>
                <p>Cadastre, edite, ative, inative e gerencie variações dos produtos.</p>
            </div>
        </div>

        <div class="actions-inline">
            <button type="button" class="btn btn-green" onclick="openCreateModal()">
                <i class="bi bi-plus-circle"></i>
                Novo produto
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
                <div class="card-title">Lista de produtos</div>
                <div class="card-subtitle">Todos os produtos cadastrados no sistema.</div>
            </div>
            <span class="card-count">{{ $produtos->count() }} {{ $produtos->count() === 1 ? 'produto' : 'produtos' }}</span>
        </div>

        <div class="filters-wrap">
            <form method="GET" action="{{ route('produtos.index') }}" class="filters-form">
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
                            placeholder="Buscar por nome do produto ou nome da variação..."
                        >
                    </div>
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

                    <a href="{{ route('produtos.index') }}" class="btn btn-dark">
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
                            <th>Produto</th>
                            <th>Unidade</th>
                            <th>Valor</th>
                            <th>Entrega</th>
                            <th>Variações</th>
                            <th>Status</th>
                            <th style="text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produtos as $produto)
                            @php
                                $iniciais = collect(explode(' ', trim($produto->nome)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');

                                $variacoesData = $produto->variacoes
                                    ->map(function ($v) {
                                        return [
                                            'id' => $v->id,
                                            'nome_variacao' => $v->nome_variacao,
                                            'cor' => $v->cor,
                                            'tamanho' => $v->tamanho,
                                            'ca' => $v->ca,
                                            'status' => $v->status,
                                        ];
                                    })
                                    ->values();
                            @endphp

                            <tr>
                                <td data-label="Nome">
                                    <div class="product-cell">
                                        <span class="avatar">{{ $iniciais ?: 'PR' }}</span>
                                        <div>
                                            <div class="product-cell__name">{{ $produto->nome }}</div>
                                            <div class="product-cell__sub">{{ $produto->descricao ?: '—' }}</div>

                                            @if (!$produto->controla_variacao && $produto->ca)
                                                <div class="product-cell__meta">
                                                    <i class="bi bi-upc-scan"></i>
                                                    C.A: {{ $produto->ca }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Unidade">
                                    <span class="badge-status badge-info">{{ $produto->unidade }}</span>
                                </td>

                                <td data-label="Valor">
                                    <span class="price-value">R$ {{ number_format((float) $produto->valor_unitario, 2, ',', '.') }}</span>
                                </td>

                                <td data-label="Entrega">
                                    @if (!is_null($produto->dias_entrega_media))
                                        <span class="days-pill">
                                            <i class="bi bi-truck"></i>
                                            {{ $produto->dias_entrega_media }} {{ (int) $produto->dias_entrega_media === 1 ? 'dia' : 'dias' }}
                                        </span>
                                    @else
                                        <span class="text-muted-small">—</span>
                                    @endif
                                </td>

                                <td data-label="Variações">
                                    @if ($produto->controla_variacao)
                                        @if ($produto->variacoes->isEmpty())
                                            <span class="text-muted-small">Sem variações</span>
                                        @else
                                            <div class="variacoes-wrap">
                                                @foreach ($produto->variacoes as $variacao)
                                                    <span class="variacao-pill {{ $variacao->status === 'inativo' ? 'variacao-pill--inactive' : '' }}">
                                                        {{ $variacao->nome_variacao }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted-small">Sem variações</span>
                                    @endif
                                </td>

                                <td data-label="Status">
                                    @if ($produto->status === 'ativo')
                                        <span class="badge-status badge-success">Ativo</span>
                                    @else
                                        <span class="badge-status badge-warning">Inativo</span>
                                    @endif
                                </td>

                                <td data-label="Ações">
                                    <div class="table-actions">
                                        <button
                                            type="button"
                                            class="btn btn-dark btn-icon tip"
                                            data-tip="Editar"
                                            aria-label="Editar produto"
                                            data-id="{{ $produto->id }}"
                                            data-nome="{{ $produto->nome }}"
                                            data-descricao="{{ $produto->descricao }}"
                                            data-unidade="{{ $produto->unidade }}"
                                            data-valor-unitario="{{ $produto->valor_unitario }}"
                                            data-dias-entrega-media="{{ $produto->dias_entrega_media }}"
                                            data-controla-variacao="{{ $produto->controla_variacao ? '1' : '0' }}"
                                            data-ca="{{ $produto->ca }}"
                                            data-status="{{ $produto->status }}"
                                            data-variacoes='@json($variacoesData)'
                                            onclick="openEditModalFromButton(this)"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            <span class="label-mobile">Editar</span>
                                        </button>

                                        @if ($produto->status === 'ativo')
                                            <form
                                                method="POST"
                                                action="{{ route('produtos.inativar', $produto) }}"
                                                onsubmit="return confirm('Tem certeza que deseja inativar este produto?')"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="btn btn-warning-soft btn-icon tip"
                                                    data-tip="Inativar"
                                                    aria-label="Inativar produto"
                                                >
                                                    <i class="bi bi-slash-circle"></i>
                                                    <span class="label-mobile">Inativar</span>
                                                </button>
                                            </form>
                                        @else
                                            <form
                                                method="POST"
                                                action="{{ route('produtos.ativar', $produto) }}"
                                                onsubmit="return confirm('Deseja reativar este produto?')"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="btn btn-primary-soft btn-icon tip"
                                                    data-tip="Reativar"
                                                    aria-label="Reativar produto"
                                                >
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                    <span class="label-mobile">Ativar</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-box-seam"></i>
                                        @if ($filtrosAtivos)
                                            <div class="empty-state__title">Nenhum produto encontrado</div>
                                            <div>Tente ajustar a busca ou limpar os filtros.</div>
                                        @else
                                            <div class="empty-state__title">Nenhum produto cadastrado</div>
                                            <div>Clique em “Novo produto” para começar.</div>
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
    <div class="custom-modal" id="createProdutoModal" role="dialog" aria-modal="true" aria-labelledby="createProdutoTitle">
        <div class="custom-modal-backdrop" onclick="closeCreateModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="createProdutoTitle">Novo produto</h3>
                    <p>Preencha os dados do produto e, se necessário, cadastre as variações.</p>
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

                <form action="{{ route('produtos.store') }}" method="POST">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-tag"></i> Nome</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-tag input-icon"></i>
                                <input
                                    type="text"
                                    name="nome"
                                    class="form-control-custom"
                                    value="{{ old('nome') }}"
                                    placeholder="Ex.: Bota de segurança"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-rulers"></i> Unidade de distribuição</label>
                            <select name="unidade" class="form-control-custom" required>
                                <option value="">Selecione</option>
                                @foreach ($unidades as $valor => $label)
                                    <option value="{{ $valor }}" {{ old('unidade') === $valor ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-cash-coin"></i> Valor unitário</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-currency-dollar input-icon"></i>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="valor_unitario"
                                    class="form-control-custom"
                                    value="{{ old('valor_unitario', '0.00') }}"
                                    placeholder="0,00"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-truck"></i> Média de dias de entrega</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-calendar3 input-icon"></i>
                                <input
                                    type="number"
                                    min="0"
                                    name="dias_entrega_media"
                                    class="form-control-custom"
                                    value="{{ old('dias_entrega_media') }}"
                                    placeholder="Ex.: 7"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-toggle-on"></i> Status</label>
                            <select name="status" class="form-control-custom" required>
                                <option value="ativo" {{ old('status', 'ativo') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-diagram-3"></i> Controla variações?</label>
                            <label class="toggle-line">
                                <input
                                    type="checkbox"
                                    name="controla_variacao"
                                    id="create_controla_variacao"
                                    value="1"
                                    {{ old('controla_variacao') ? 'checked' : '' }}
                                    onchange="toggleCreateVariacoesImmediate()"
                                >
                                <span class="toggle-line__label">
                                    <strong>Sim, este produto terá variações</strong>
                                    <small>Cadastre tamanhos, cores ou outros atributos como itens independentes.</small>
                                </span>
                            </label>
                        </div>

                        <div
                            class="form-group form-group-full"
                            id="create_ca_wrapper"
                            style="{{ old('controla_variacao') ? 'display:none;' : 'display:block;' }}"
                        >
                            <label class="form-label"><i class="bi bi-upc-scan"></i> C.A do produto</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-upc-scan input-icon"></i>
                                <input
                                    type="text"
                                    name="ca"
                                    id="create_ca"
                                    class="form-control-custom"
                                    value="{{ old('ca') }}"
                                    placeholder="Informe o C.A quando o produto não tiver variações"
                                >
                            </div>
                        </div>

                        <div
                            class="form-group form-group-full"
                            id="create_variacoes_wrapper"
                            style="{{ old('controla_variacao') ? 'display:block;' : 'display:none;' }}"
                        >
                            <div class="variacoes-section">
                                <div class="variacoes-section__title">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                    Variações do produto
                                    <span class="variacoes-section__count" id="create_variacoes_count">0</span>
                                </div>
                                <div class="variacoes-section__hint">
                                    Exemplo: Bota azul 45, Bota branca 46, Luva P, Luva M...
                                </div>

                                <div id="create_lista_variacoes" class="variacoes-lista"></div>

                                <div class="actions-inline" style="margin-top:12px;">
                                    <button type="button" class="btn-mini" onclick="adicionarVariacaoCreate()">
                                        <i class="bi bi-plus-circle"></i>
                                        Adicionar variação
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-card-text"></i> Descrição</label>
                            <textarea
                                name="descricao"
                                class="form-control-custom"
                                rows="4"
                                placeholder="Descreva o produto (opcional)"
                            >{{ old('descricao') }}</textarea>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content:flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeCreateModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Salvar produto
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div class="custom-modal" id="editProdutoModal" role="dialog" aria-modal="true" aria-labelledby="editProdutoTitle">
        <div class="custom-modal-backdrop" onclick="closeEditModal()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h3 id="editProdutoTitle">Editar produto</h3>
                    <p>Atualize os dados do produto e gerencie as variações.</p>
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

                <form id="editProdutoForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-tag"></i> Nome</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-tag input-icon"></i>
                                <input type="text" name="nome" id="edit_nome" class="form-control-custom" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-rulers"></i> Unidade de distribuição</label>
                            <select name="unidade" id="edit_unidade" class="form-control-custom" required>
                                <option value="">Selecione</option>
                                @foreach ($unidades as $valor => $label)
                                    <option value="{{ $valor }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-cash-coin"></i> Valor unitário</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-currency-dollar input-icon"></i>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="valor_unitario"
                                    id="edit_valor_unitario"
                                    class="form-control-custom"
                                    placeholder="0,00"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-truck"></i> Média de dias de entrega</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-calendar3 input-icon"></i>
                                <input
                                    type="number"
                                    min="0"
                                    name="dias_entrega_media"
                                    id="edit_dias_entrega_media"
                                    class="form-control-custom"
                                    placeholder="Ex.: 7"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="bi bi-toggle-on"></i> Status</label>
                            <select name="status" id="edit_status" class="form-control-custom" required>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-diagram-3"></i> Controla variações?</label>
                            <label class="toggle-line">
                                <input
                                    type="checkbox"
                                    name="controla_variacao"
                                    id="edit_controla_variacao"
                                    value="1"
                                    onchange="toggleEditVariacoesImmediate()"
                                >
                                <span class="toggle-line__label">
                                    <strong>Sim, este produto terá variações</strong>
                                    <small>Cadastre tamanhos, cores ou outros atributos como itens independentes.</small>
                                </span>
                            </label>
                        </div>

                        <div class="form-group form-group-full" id="edit_ca_wrapper" style="display:block;">
                            <label class="form-label"><i class="bi bi-upc-scan"></i> C.A do produto</label>
                            <div class="input-wrap has-icon">
                                <i class="bi bi-upc-scan input-icon"></i>
                                <input
                                    type="text"
                                    name="ca"
                                    id="edit_ca"
                                    class="form-control-custom"
                                    placeholder="Informe o C.A quando o produto não tiver variações"
                                >
                            </div>
                        </div>

                        <div class="form-group form-group-full" id="edit_variacoes_wrapper" style="display:none;">
                            <div class="variacoes-section">
                                <div class="variacoes-section__title">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                    Variações do produto
                                    <span class="variacoes-section__count" id="edit_variacoes_count">0</span>
                                </div>
                                <div class="variacoes-section__hint">
                                    Exemplo: Bota azul 45, Bota branca 46, Luva P, Luva M...
                                </div>

                                <div id="edit_lista_variacoes" class="variacoes-lista"></div>

                                <div class="actions-inline" style="margin-top:12px;">
                                    <button type="button" class="btn-mini" onclick="adicionarVariacaoEdit()">
                                        <i class="bi bi-plus-circle"></i>
                                        Adicionar variação
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-full">
                            <label class="form-label"><i class="bi bi-card-text"></i> Descrição</label>
                            <textarea
                                name="descricao"
                                id="edit_descricao"
                                class="form-control-custom"
                                rows="4"
                                placeholder="Descreva o produto (opcional)"
                            ></textarea>
                        </div>

                        <div class="form-group-full actions-inline" style="justify-content:flex-end; margin-top: 6px;">
                            <button type="button" class="btn btn-dark" onclick="closeEditModal()">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check2-circle"></i> Atualizar produto
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            let createVariacaoIndex = 0;
            let editVariacaoIndex = 0;
            let lastFocused = null;

            function escapeHtml(str) {
                return String(str ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#39;');
            }

            function criarBlocoVariacao(index, dados = {}, prefixo = 'variacoes') {
                const id = escapeHtml(dados.id ?? '');
                const nome = escapeHtml(dados.nome_variacao ?? '');
                const cor = escapeHtml(dados.cor ?? '');
                const tamanho = escapeHtml(dados.tamanho ?? '');
                const ca = escapeHtml(dados.ca ?? '');
                const status = (dados.status ?? 'ativo');

                return `
                    <div class="variacao-item">
                        <input type="hidden" name="${prefixo}[${index}][id]" value="${id}">

                        <div class="variacao-item__head">
                            <div class="variacao-item__number">
                                <span class="variacao-item__badge">${index + 1}</span>
                                <span>Variação ${index + 1}</span>
                            </div>
                            <button type="button" class="btn-mini btn-mini--danger"
                                onclick="removerVariacao(this, '${prefixo === 'variacoes' ? 'create' : 'edit'}')">
                                <i class="bi bi-trash"></i> Remover
                            </button>
                        </div>

                        <div class="form-grid">
                            <div class="form-group form-group-full">
                                <label class="form-label"><i class="bi bi-tag"></i> Nome da variação</label>
                                <div class="input-wrap has-icon">
                                    <i class="bi bi-tag input-icon"></i>
                                    <input type="text" name="${prefixo}[${index}][nome_variacao]"
                                        class="form-control-custom" value="${nome}" placeholder="Ex.: Bota azul 45">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="bi bi-palette"></i> Cor</label>
                                <div class="input-wrap has-icon">
                                    <i class="bi bi-palette input-icon"></i>
                                    <input type="text" name="${prefixo}[${index}][cor]"
                                        class="form-control-custom" value="${cor}" placeholder="Ex.: Azul">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="bi bi-rulers"></i> Tamanho</label>
                                <div class="input-wrap has-icon">
                                    <i class="bi bi-rulers input-icon"></i>
                                    <input type="text" name="${prefixo}[${index}][tamanho]"
                                        class="form-control-custom" value="${tamanho}" placeholder="Ex.: 45">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="bi bi-upc-scan"></i> C.A</label>
                                <div class="input-wrap has-icon">
                                    <i class="bi bi-upc-scan input-icon"></i>
                                    <input type="text" name="${prefixo}[${index}][ca]"
                                        class="form-control-custom" value="${ca}" placeholder="Informe o C.A">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="bi bi-toggle-on"></i> Status</label>
                                <select name="${prefixo}[${index}][status]" class="form-control-custom">
                                    <option value="ativo" ${status === 'ativo' ? 'selected' : ''}>Ativo</option>
                                    <option value="inativo" ${status === 'inativo' ? 'selected' : ''}>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
            }

            function updateVariacoesCount(prefix) {
                const lista = document.getElementById(`${prefix}_lista_variacoes`);
                const counter = document.getElementById(`${prefix}_variacoes_count`);
                if (!lista || !counter) return;
                counter.textContent = lista.children.length;
            }

            function adicionarVariacaoCreate(dados = {}) {
                const lista = document.getElementById('create_lista_variacoes');
                if (!lista) return;

                lista.insertAdjacentHTML('beforeend', criarBlocoVariacao(createVariacaoIndex++, dados, 'variacoes'));
                updateVariacoesCount('create');
            }

            function adicionarVariacaoEdit(dados = {}) {
                const lista = document.getElementById('edit_lista_variacoes');
                if (!lista) return;

                lista.insertAdjacentHTML('beforeend', criarBlocoVariacao(editVariacaoIndex++, dados, 'variacoes'));
                updateVariacoesCount('edit');
            }

            function removerVariacao(btn, prefix) {
                const item = btn.closest('.variacao-item');
                if (item) item.remove();
                updateVariacoesCount(prefix);
            }

            function toggleCreateVariacoesImmediate() {
                const checkbox = document.getElementById('create_controla_variacao');
                const wrapper = document.getElementById('create_variacoes_wrapper');
                const lista = document.getElementById('create_lista_variacoes');
                const caWrapper = document.getElementById('create_ca_wrapper');

                if (!checkbox || !wrapper || !lista || !caWrapper) return;

                if (checkbox.checked) {
                    wrapper.style.display = 'block';
                    caWrapper.style.display = 'none';

                    if (lista.children.length === 0) {
                        adicionarVariacaoCreate();
                    }
                } else {
                    wrapper.style.display = 'none';
                    caWrapper.style.display = 'block';
                }

                updateVariacoesCount('create');
            }

            function toggleEditVariacoesImmediate() {
                const checkbox = document.getElementById('edit_controla_variacao');
                const wrapper = document.getElementById('edit_variacoes_wrapper');
                const lista = document.getElementById('edit_lista_variacoes');
                const caWrapper = document.getElementById('edit_ca_wrapper');

                if (!checkbox || !wrapper || !lista || !caWrapper) return;

                if (checkbox.checked) {
                    wrapper.style.display = 'block';
                    caWrapper.style.display = 'none';

                    if (lista.children.length === 0) {
                        adicionarVariacaoEdit();
                    }
                } else {
                    wrapper.style.display = 'none';
                    caWrapper.style.display = 'block';
                }

                updateVariacoesCount('edit');
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

            function openCreateModal() {
                openModal(document.getElementById('createProdutoModal'));
                toggleCreateVariacoesImmediate();
            }

            function closeCreateModal() {
                closeModal(document.getElementById('createProdutoModal'));
            }

            function openEditModal(id, nome, descricao, unidade, valorUnitario, diasEntregaMedia, controlaVariacao, ca, status, variacoes) {
                variacoes = variacoes || [];

                const modal = document.getElementById('editProdutoModal');
                const form = document.getElementById('editProdutoForm');
                const lista = document.getElementById('edit_lista_variacoes');

                if (!modal || !form || !lista) return;

                form.action = `/gerenciar-produtos/${id}`;

                document.getElementById('edit_nome').value = nome ?? '';
                document.getElementById('edit_descricao').value = descricao ?? '';
                document.getElementById('edit_unidade').value = unidade ?? '';
                document.getElementById('edit_valor_unitario').value = valorUnitario ?? '0.00';
                document.getElementById('edit_dias_entrega_media').value = diasEntregaMedia ?? '';
                document.getElementById('edit_ca').value = ca ?? '';
                document.getElementById('edit_status').value = status ?? 'ativo';
                document.getElementById('edit_controla_variacao').checked = String(controlaVariacao) === '1';

                lista.innerHTML = '';
                editVariacaoIndex = 0;

                if (Array.isArray(variacoes) && variacoes.length > 0) {
                    variacoes.forEach(function (variacao) {
                        adicionarVariacaoEdit(variacao);
                    });
                }

                toggleEditVariacoesImmediate();
                openModal(modal);
            }

            function openEditModalFromButton(button) {
                let variacoes = [];

                try {
                    variacoes = JSON.parse(button.dataset.variacoes || '[]');
                } catch (e) {
                    console.error('Erro ao converter variações:', e);
                }

                openEditModal(
                    button.dataset.id,
                    button.dataset.nome,
                    button.dataset.descricao,
                    button.dataset.unidade,
                    button.dataset.valorUnitario,
                    button.dataset.diasEntregaMedia,
                    button.dataset.controlaVariacao,
                    button.dataset.ca,
                    button.dataset.status,
                    variacoes
                );
            }

            function closeEditModal() {
                closeModal(document.getElementById('editProdutoModal'));
            }

            function initProdutosPage() {
                const createModal = document.getElementById('createProdutoModal');
                const editModal = document.getElementById('editProdutoModal');

                if (!createModal || !editModal) return;

                updateVariacoesCount('create');
                updateVariacoesCount('edit');

                const createCheckbox = document.getElementById('create_controla_variacao');
                const editCheckbox = document.getElementById('edit_controla_variacao');

                if (createCheckbox) {
                    const createLista = document.getElementById('create_lista_variacoes');
                    if (!createCheckbox.checked && createLista) {
                        createLista.innerHTML = createLista.innerHTML;
                    }
                    toggleCreateVariacoesImmediate();
                }

                if (editCheckbox) {
                    toggleEditVariacoesImmediate();
                }

                @if (session('open_create_modal'))
                    openCreateModal();

                    @if (old('controla_variacao') && is_array(old('variacoes')))
                        (function () {
                            const lista = document.getElementById('create_lista_variacoes');
                            if (!lista) return;

                            lista.innerHTML = '';
                            createVariacaoIndex = 0;

                            @foreach (old('variacoes') as $variacao)
                                adicionarVariacaoCreate({
                                    id: @json($variacao['id'] ?? ''),
                                    nome_variacao: @json($variacao['nome_variacao'] ?? ''),
                                    cor: @json($variacao['cor'] ?? ''),
                                    tamanho: @json($variacao['tamanho'] ?? ''),
                                    ca: @json($variacao['ca'] ?? ''),
                                    status: @json($variacao['status'] ?? 'ativo'),
                                });
                            @endforeach
                        })();
                    @endif
                @endif

                @if (session('open_edit_modal'))
                    @if ($produtoEditSession)
                        @php
                            $produtoEditVariacoes = $produtoEditSession->variacoes->map(function ($v) {
                                return [
                                    'id' => $v->id,
                                    'nome_variacao' => $v->nome_variacao,
                                    'cor' => $v->cor,
                                    'tamanho' => $v->tamanho,
                                    'ca' => $v->ca,
                                    'status' => $v->status,
                                ];
                            })->values();
                        @endphp

                        openEditModal(
                            '{{ $produtoEditSession->id }}',
                            @json($produtoEditSession->nome),
                            @json($produtoEditSession->descricao),
                            @json($produtoEditSession->unidade),
                            @json($produtoEditSession->valor_unitario),
                            @json($produtoEditSession->dias_entrega_media),
                            @json($produtoEditSession->controla_variacao ? 1 : 0),
                            @json($produtoEditSession->ca),
                            @json($produtoEditSession->status),
                            @json($produtoEditVariacoes)
                        );
                    @endif
                @endif
            }

            window.adicionarVariacaoCreate = adicionarVariacaoCreate;
            window.adicionarVariacaoEdit = adicionarVariacaoEdit;
            window.removerVariacao = removerVariacao;
            window.toggleCreateVariacoesImmediate = toggleCreateVariacoesImmediate;
            window.toggleEditVariacoesImmediate = toggleEditVariacoesImmediate;
            window.openCreateModal = openCreateModal;
            window.closeCreateModal = closeCreateModal;
            window.openEditModal = openEditModal;
            window.openEditModalFromButton = openEditModalFromButton;
            window.closeEditModal = closeEditModal;
            window.initProdutosPage = initProdutosPage;

            document.addEventListener('DOMContentLoaded', initProdutosPage);
            document.addEventListener('page:updated', initProdutosPage);
            document.addEventListener('turbo:load', initProdutosPage);
            document.addEventListener('livewire:navigated', initProdutosPage);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeCreateModal();
                    closeEditModal();
                }
            });
        })();
    </script>
@endsection