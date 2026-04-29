@extends('layouts.app')

@section('title', 'Relatório - Comprovantes pendentes')
@section('pageTitle', 'Relatório - Comprovantes pendentes')
@section('pageDescription', 'Controle as entregas que ainda precisam de comprovante anexado.')

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

    .badge-warning {
        background: var(--warning-soft);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.22);
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

    .text-muted-small {
        font-size: 12px;
        color: var(--text-muted);
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
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-file-earmark-excel"></i></div>
            <div class="page-head__text">
                <h2>Comprovantes pendentes</h2>
                <p>Veja quais entregas ainda não possuem comprovante anexado.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('relatorios.comprovantes.pdf') }}" class="btn btn-dark">
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
                <div class="card-subtitle">Entregas com status de comprovante pendente.</div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Entrega</th>
                            <th>Data</th>
                            <th>Funcionário</th>
                            <th>Obra</th>
                            <th>Registrado por</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dados as $item)
                            @php
                                $nomeFuncionario = $item->funcionario->nome ?? '-';
                                $iniciais = collect(explode(' ', trim($nomeFuncionario)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');
                            @endphp
                            <tr>
                                <td data-label="Entrega">
                                    <div class="entity-cell">
                                        <span class="entity-avatar">#</span>
                                        <div>
                                            <div class="entity-cell__name">Entrega #{{ $item->id }}</div>
                                            <div class="entity-cell__sub">
                                                {{ $item->data_entrega?->format('d/m/Y') ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Data">
                                    {{ $item->data_entrega?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td data-label="Funcionário">
                                    <div class="entity-cell">
                                        <span class="entity-avatar">{{ $iniciais ?: '?' }}</span>
                                        <div>
                                            <div class="entity-cell__name">{{ $item->funcionario->nome ?? '-' }}</div>
                                            <div class="entity-cell__sub">
                                                {{ $item->funcionario->matricula ?? 'Sem matrícula' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Obra">
                                    <div class="entity-cell__name">{{ $item->obra->nome ?? '-' }}</div>
                                </td>

                                <td data-label="Registrado por">
                                    <div class="entity-cell__name">{{ $item->usuario->name ?? 'Sistema' }}</div>
                                </td>

                                <td data-label="Status">
                                    <span class="badge-status badge-warning">Pendente</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-check2-circle"></i>
                                        <div class="empty-state__title">Nenhum comprovante pendente</div>
                                        <div>Não há entregas aguardando comprovante no momento.</div>
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