@extends('layouts.app')

@section('title', 'Solicitação de Abastecimento')
@section('pageTitle', 'Solicitação de Abastecimento')
@section('pageDescription', 'Solicite abastecimento escolhendo um veículo disponível.')
@section('content')
    <style>
    .page-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .page-head h2,
    .page-head p {
        margin: 0;
    }

    .page-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-dark-primary {
        border: none;
        border-radius: 14px;
        padding: 10px 16px;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        box-shadow: 0 10px 25px rgba(37, 99, 235, .25);
    }

    .btn-dark-primary:hover {
        transform: translateY(-1px);
        color: #fff;
    }

    .btn-dark-primary[disabled] {
        opacity: .55;
        cursor: not-allowed;
        transform: none;
    }

    .btn-outline-dark-custom {
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 12px;
        padding: 9px 14px;
        font-weight: 600;
        color: #e2e8f0;
        background: rgba(15, 23, 42, 0.72);
    }

    .btn-outline-dark-custom:hover {
        color: #fff;
        background: rgba(30, 41, 59, 0.92);
    }

    .grid-top {
        display: grid;
        grid-template-columns: 1.1fr 2fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    .dark-card {
        background: linear-gradient(180deg, rgba(15, 23, 42, .98), rgba(10, 15, 28, .98));
        border: 1px solid rgba(148, 163, 184, .14);
        border-radius: 20px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, .22);
        overflow: hidden;
    }

    .dark-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, .12);
    }

    .dark-card-header h3 {
        margin: 0;
        color: #f8fafc;
        font-size: 1.02rem;
        font-weight: 700;
    }

    .dark-card-header p {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: .9rem;
    }

    .dark-card-body {
        padding: 20px;
    }

    .vehicle-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .vehicle-highlight {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 16px;
        border-radius: 16px;
        background: rgba(30, 41, 59, .55);
        border: 1px solid rgba(148, 163, 184, .10);
    }

    .vehicle-highlight strong {
        color: #f8fafc;
        font-size: 1rem;
    }

    .vehicle-highlight span {
        color: #94a3b8;
        font-size: .9rem;
    }

    .mini-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .mini-stat {
        background: rgba(15, 23, 42, .75);
        border: 1px solid rgba(148, 163, 184, .10);
        border-radius: 16px;
        padding: 14px;
    }

    .mini-stat-label {
        color: #94a3b8;
        font-size: .78rem;
        display: block;
        margin-bottom: 6px;
    }

    .mini-stat-value {
        color: #f8fafc;
        font-size: 1rem;
        font-weight: 700;
    }

    .alert-custom {
        border-radius: 16px;
        padding: 14px 16px;
        margin-bottom: 16px;
        border: 1px solid transparent;
    }

    .alert-success-custom {
        color: #bbf7d0;
        background: rgba(20, 83, 45, .25);
        border-color: rgba(74, 222, 128, .14);
    }

    .alert-danger-custom {
        color: #fecaca;
        background: rgba(127, 29, 29, .24);
        border-color: rgba(248, 113, 113, .14);
    }

    .alert-warning-custom {
        color: #fde68a;
        background: rgba(120, 53, 15, .28);
        border-color: rgba(251, 191, 36, .16);
    }

    .table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .table-dark-custom {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-dark-custom thead th {
        color: #94a3b8;
        font-size: .82rem;
        font-weight: 700;
        border: none;
        padding: 0 14px 10px;
        white-space: nowrap;
    }

    .table-dark-custom tbody tr {
        background: rgba(15, 23, 42, .86);
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, .08);
    }

    .table-dark-custom tbody td {
        color: #e2e8f0;
        border: none;
        padding: 14px;
        vertical-align: middle;
    }

    .table-dark-custom tbody tr td:first-child {
        border-radius: 14px 0 0 14px;
    }

    .table-dark-custom tbody tr td:last-child {
        border-radius: 0 14px 14px 0;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 700;
    }

    .badge-status.pendente {
        background: rgba(245, 158, 11, .14);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, .16);
    }

    .badge-status.aprovada {
        background: rgba(16, 185, 129, .15);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, .2);
    }

    .badge-status.reprovada {
        background: rgba(239, 68, 68, .12);
        color: #f87171;
        border: 1px solid rgba(248, 113, 113, .16);
    }

    .badge-status.ajustada {
        background: rgba(59, 130, 246, .14);
        color: #93c5fd;
        border: 1px solid rgba(147, 197, 253, .16);
    }

    .badge-status.enviado {
        background: rgba(16, 185, 129, .15);
        color: #6ee7b7;
        border: 1px solid rgba(110, 231, 183, .18);
    }

    .empty-state {
        padding: 34px 20px;
        text-align: center;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 2rem;
        display: block;
        margin-bottom: 10px;
        color: #475569;
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
        width: min(760px, calc(100% - 24px));
        margin: auto;
        top: 50%;
        transform: translateY(-50%);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 20px;
        overflow: hidden;
        color: #e2e8f0;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
        z-index: 2;
    }

    .custom-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px 18px;
        background: linear-gradient(180deg, rgba(37, 99, 235, 0.08), transparent 70%);
        border-bottom: 1px solid rgba(148, 163, 184, .12);
        position: relative;
    }

    .custom-modal-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #2563eb, transparent);
        opacity: .55;
    }

    .custom-modal-header h5 {
        margin: 0 0 4px;
        color: #f8fafc;
        font-size: 19px;
        font-weight: 700;
    }

    .custom-modal-header p {
        margin: 0;
        color: #94a3b8;
        font-size: 13.5px;
        line-height: 1.45;
    }

    .custom-modal-close {
        border: 1px solid rgba(148, 163, 184, .24);
        background: rgba(15, 23, 42, 0.7);
        color: #e2e8f0;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color .26s ease, transform .18s ease;
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
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #94a3b8;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .form-control-custom,
    .form-select-custom {
        width: 100%;
        min-height: 48px;
        border-radius: 11px;
        border: 1px solid rgba(148, 163, 184, .24);
        background: #0b1220;
        color: #f8fafc;
        padding: 12px 14px;
        outline: none;
        box-shadow: none;
        font-size: 14.5px;
        transition: border-color .26s ease, box-shadow .26s ease, background-color .26s ease;
        appearance: none;
        -webkit-appearance: none;
    }

    .form-control-custom::placeholder {
        color: #64748b;
    }

    .form-control-custom:hover,
    .form-select-custom:hover {
        border-color: rgba(148, 163, 184, .34);
    }

    .form-control-custom:focus,
    .form-select-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .18);
        background: #111827;
    }

    .form-select-custom {
        padding-right: 40px;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
        background-position: right 14px center;
        background-repeat: no-repeat;
    }

    textarea.form-control-custom {
        min-height: 110px;
        resize: vertical;
        padding-left: 14px;
    }

    .form-help {
        color: #94a3b8;
        font-size: .82rem;
        line-height: 1.4;
    }

    .preview-box {
        display: none;
        margin-top: 10px;
    }

    .preview-box.active {
        display: block;
    }

    .preview-box img {
        width: 100%;
        max-width: 260px;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, .18);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .25);
    }

    .thumb-foto {
        width: 58px;
        height: 58px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, .16);
        display: block;
    }

    .thumb-group {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .thumb-stack {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .modal-footer-custom {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
        flex-wrap: wrap;
    }

    .btn-secondary-dark {
        border: 1px solid rgba(148, 163, 184, .24);
        background: rgba(15, 23, 42, 0.72);
        color: #e2e8f0;
        border-radius: 11px;
        padding: 10px 16px;
        font-weight: 600;
    }

    .btn-secondary-dark:hover {
        color: #fff;
        background: rgba(30, 41, 59, 0.92);
    }

    .mobile-cards {
        display: none;
    }

    .request-mobile-card {
        background: rgba(15, 23, 42, .92);
        border: 1px solid rgba(148, 163, 184, .10);
        border-radius: 18px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .mobile-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .mobile-label {
        color: #94a3b8;
        font-size: .8rem;
        display: block;
        margin-bottom: 3px;
    }

    .mobile-value {
        color: #f8fafc;
        font-weight: 600;
        font-size: .92rem;
    }

    .action-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }

    .modal-open {
        overflow: hidden;
    }

    .camera-box video {
        width: 100%;
        border-radius: 14px;
        background: #000;
        max-height: 65vh;
        object-fit: cover;
    }

    .camera-actions {
        display: flex;
        gap: 10px;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .camera-status {
        margin-top: 10px;
        color: #94a3b8;
        font-size: .85rem;
    }

    .meta-help {
        color: #94a3b8;
        font-size: .8rem;
    }

    @media (max-width: 900px) {
        .grid-top,
        .form-grid {
            grid-template-columns: 1fr;
        }

        .mini-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .desktop-table {
            display: none;
        }

        .mobile-cards {
            display: grid;
            gap: 14px;
        }

        .page-head {
            flex-direction: column;
            align-items: stretch;
        }

        .page-actions {
            width: 100%;
        }

        .page-actions .btn-dark-primary {
            width: 100%;
            justify-content: center;
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

        .camera-actions .btn,
        .modal-footer-custom .btn,
        .action-stack .btn {
            width: 100%;
        }

        .action-stack {
            align-items: stretch;
        }
    }
</style>

    @php
    $temVeiculoAtivo = $veiculos->isNotEmpty();
    $pendentes = $solicitacoes->where('status', 'pendente')->count();
    $aprovadas = $solicitacoes->where('status', 'aprovada')->count() + $solicitacoes->where('status', 'ajustada')->count();
    $reprovadas = $solicitacoes->where('status', 'reprovada')->count();

    $fotoUrl = function ($path) {
        if (!$path) return null;

        $path = ltrim($path, '/');
        $path = str_replace('public/', '', $path);
        $path = str_replace('storage/', '', $path);

        return url('storage/app/public/' . $path);
    };
@endphp

    <div class="page-head">
        <div>
            <h2>Solicitação de abastecimento</h2>
            <p>Envie solicitações para o veículo vinculado ao seu usuário.</p>
        </div>

        <div class="page-actions">
            @if ($temVeiculoAtivo)
                <button type="button" class="btn btn-dark-primary" onclick="abrirModalSolicitacao()">
                    <i class="bi bi-plus-circle"></i> Nova solicitação
                </button>
            @else
                <button type="button" class="btn btn-dark-primary" disabled>
                    <i class="bi bi-plus-circle"></i> Nova solicitação
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert-custom alert-success-custom">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-custom alert-danger-custom">
            {{ session('error') }}
        </div>
    @endif

    @if (!$temVeiculoAtivo)
    <div class="alert-custom alert-warning-custom">
        Nenhum veículo ativo cadastrado. Cadastre ou ative um veículo para enviar solicitações.
    </div>
@endif

    @if ($errors->any())
        <div class="alert-custom alert-danger-custom">
            <strong>Corrija os campos abaixo:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<div class="grid-top">
    <div class="dark-card">
    <div class="dark-card-header">
        <h3>Veículos disponíveis</h3>
        <p>Escolha o veículo no momento de abrir uma nova solicitação.</p>
    </div>
    <div class="dark-card-body">
        <div class="vehicle-box">
            <div class="vehicle-highlight">
                <strong>{{ $veiculos->count() }} veículo(s) ativo(s)</strong>
                <span>
                    A solicitação não depende mais de veículo vinculado ao usuário.
                </span>
                <span>
                    Selecione o veículo desejado dentro do formulário.
                </span>
            </div>
        </div>
    </div>
</div>

        <div class="dark-card">
            <div class="dark-card-header">
                <h3>Resumo</h3>
                <p>Visão rápida das suas solicitações.</p>
            </div>
            <div class="dark-card-body">
                <div class="mini-stats">
                    <div class="mini-stat">
                        <span class="mini-stat-label">Total</span>
                        <span class="mini-stat-value">{{ $solicitacoes->count() }}</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Pendentes</span>
                        <span class="mini-stat-value">{{ $pendentes }}</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Aprovadas/Ajustadas</span>
                        <span class="mini-stat-value">{{ $aprovadas }}</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Reprovadas</span>
                        <span class="mini-stat-value">{{ $reprovadas }}</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Usuário</span>
                        <span class="mini-stat-value">{{ $user->name }}</span>
                    </div>
                    <div class="mini-stat">
    <span class="mini-stat-label">Veículos ativos</span>
    <span class="mini-stat-value">{{ $veiculos->count() }}</span>
</div>
                </div>
            </div>
        </div>
    </div>

    <div class="dark-card">
        <div class="dark-card-header">
            <h3>Histórico de solicitações</h3>
            <p>Acompanhe o status de cada pedido e envie o comprovante após aprovação.</p>
        </div>

        <div class="dark-card-body">
            @if ($solicitacoes->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-fuel-pump"></i>
                    Nenhuma solicitação cadastrada ainda.
                </div>
            @else
                <div class="desktop-table">
                    <div class="table-wrap">
                        <table class="table-dark-custom">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Veículo</th>
                                    <th>KM</th>
                                    <th>Painel</th>
                                    <th>Tipo</th>
                                    <th>Solicitado</th>
                                    <th>Aprovado</th>
                                    <th>Status</th>
                                    <th>Comprovante</th>
                                    <th>Obs. usuário</th>
                                    <th>Obs. admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitacoes as $solicitacao)
                                    <tr>
    <td>{{ optional($solicitacao->data_solicitacao)->format('d/m/Y') }}</td>

    <td>
        @if ($solicitacao->veiculo)
            <strong>{{ $solicitacao->veiculo->placa }}</strong><br>
            <span style="color:#94a3b8; font-size:.82rem;">
                {{ trim(($solicitacao->veiculo->marca ?? '') . ' ' . ($solicitacao->veiculo->modelo ?? '')) }}
            </span>
        @else
            —
        @endif
    </td>

    <td>{{ number_format((float) $solicitacao->km_informado, 1, ',', '.') }}</td>
                                        <td>
                                            @if ($solicitacao->foto_painel)
                                                <a href="{{ $fotoUrl($solicitacao->foto_painel) }}" target="_blank">
                                                    <img
                                                        src="{{ $fotoUrl($solicitacao->foto_painel) }}"
                                                        alt="Foto do painel"
                                                        class="thumb-foto"
                                                    >
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ strtoupper($solicitacao->tipo_solicitacao) }}</td>
                                        <td>{{ number_format((float) $solicitacao->quantidade_solicitada, 2, ',', '.') }}</td>
                                        <td>
                                            {{ $solicitacao->quantidade_aprovada !== null
                                                ? number_format((float) $solicitacao->quantidade_aprovada, 2, ',', '.')
                                                : '—' }}
                                        </td>
                                        <td>
                                            <span class="badge-status {{ $solicitacao->status }}">
                                                {{ strtoupper($solicitacao->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-stack">
                                                @if ($solicitacao->status_comprovante === 'enviado' && $solicitacao->foto_nota && $solicitacao->foto_selfie)
                                                    <span class="badge-status enviado">ENVIADO</span>

                                                    <div class="thumb-stack">
                                                        <a href="{{ asset('storage/' . $solicitacao->foto_nota) }}" target="_blank" title="Foto da nota">
                                                            <img src="{{ asset('storage/' . $solicitacao->foto_nota) }}" class="thumb-foto" alt="Foto da nota">
                                                        </a>

                                                        <a href="{{ asset('storage/' . $solicitacao->foto_selfie) }}" target="_blank" title="Selfie do usuário">
                                                            <img src="{{ asset('storage/' . $solicitacao->foto_selfie) }}" class="thumb-foto" alt="Selfie do usuário">
                                                        </a>
                                                    </div>

                                                    <span class="meta-help">
                                                        {{ $solicitacao->comprovante_enviado_em?->format('d/m/Y H:i') }}
                                                    </span>
                                                @elseif (in_array($solicitacao->status, ['aprovada', 'ajustada']))
                                                    <button
                                                        type="button"
                                                        class="btn btn-dark-primary"
                                                        onclick="abrirModalComprovante({{ $solicitacao->id }})"
                                                    >
                                                        <i class="bi bi-camera"></i> Enviar comprovante
                                                    </button>
                                                @else
                                                    —
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $solicitacao->observacao_usuario ?: '—' }}</td>
                                        <td>{{ $solicitacao->observacao_admin ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mobile-cards">
                    @foreach ($solicitacoes as $solicitacao)
                        <div class="request-mobile-card">
                            <div class="mobile-row">
                                <div>
                                    <span class="mobile-label">Data</span>
                                    <span class="mobile-value">{{ optional($solicitacao->data_solicitacao)->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="mobile-label">Status</span>
                                    <span class="badge-status {{ $solicitacao->status }}">
                                        {{ strtoupper($solicitacao->status) }}
                                    </span>
                                </div>
                            </div>
                            <div>
    <span class="mobile-label">Veículo</span>
    <span class="mobile-value">
        @if ($solicitacao->veiculo)
            {{ $solicitacao->veiculo->placa }}
            —
            {{ trim(($solicitacao->veiculo->marca ?? '') . ' ' . ($solicitacao->veiculo->modelo ?? '')) }}
        @else
            —
        @endif
    </span>
</div>

                            <div class="mobile-row">
                                <div>
                                    <span class="mobile-label">KM</span>
                                    <span class="mobile-value">{{ number_format((float) $solicitacao->km_informado, 1, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="mobile-label">Tipo</span>
                                    <span class="mobile-value">{{ strtoupper($solicitacao->tipo_solicitacao) }}</span>
                                </div>
                            </div>

                            <div class="mobile-row">
                                <div>
                                    <span class="mobile-label">Solicitado</span>
                                    <span class="mobile-value">{{ number_format((float) $solicitacao->quantidade_solicitada, 2, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="mobile-label">Aprovado</span>
                                    <span class="mobile-value">
                                        {{ $solicitacao->quantidade_aprovada !== null
                                            ? number_format((float) $solicitacao->quantidade_aprovada, 2, ',', '.')
                                            : '—' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="mobile-label">Foto do painel</span>
                                @if ($solicitacao->foto_painel)
                                    <a href="{{ $fotoUrl($solicitacao->foto_painel) }}" target="_blank">
                                        <img
                                            src="{{ $fotoUrl($solicitacao->foto_painel) }}"
                                            alt="Foto do painel"
                                            class="thumb-foto"
                                        >
                                    </a>
                                @else
                                    <span class="mobile-value">—</span>
                                @endif
                            </div>

                            <div>
                                <span class="mobile-label">Comprovante</span>

                                @if ($solicitacao->status_comprovante === 'enviado' && $solicitacao->foto_nota && $solicitacao->foto_selfie)
                                    <div class="action-stack">
                                        <span class="badge-status enviado">ENVIADO</span>

                                        <div class="thumb-stack">
                                            <a href="{{ asset('storage/' . $solicitacao->foto_nota) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $solicitacao->foto_nota) }}" class="thumb-foto" alt="Foto da nota">
                                            </a>

                                            <a href="{{ asset('storage/' . $solicitacao->foto_selfie) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $solicitacao->foto_selfie) }}" class="thumb-foto" alt="Selfie do usuário">
                                            </a>
                                        </div>

                                        <span class="meta-help">
                                            {{ $solicitacao->comprovante_enviado_em?->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                @elseif (in_array($solicitacao->status, ['aprovada', 'ajustada']))
                                    <button
                                        type="button"
                                        class="btn btn-dark-primary"
                                        onclick="abrirModalComprovante({{ $solicitacao->id }})"
                                    >
                                        <i class="bi bi-camera"></i> Enviar comprovante
                                    </button>
                                @else
                                    <span class="mobile-value">—</span>
                                @endif
                            </div>

                            <div>
                                <span class="mobile-label">Observação do usuário</span>
                                <span class="mobile-value">{{ $solicitacao->observacao_usuario ?: '—' }}</span>
                            </div>

                            <div>
                                <span class="mobile-label">Observação do admin</span>
                                <span class="mobile-value">{{ $solicitacao->observacao_admin ?: '—' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($temVeiculoAtivo)
    <div class="custom-modal" id="modalSolicitacao" role="dialog" aria-modal="true" aria-labelledby="modalSolicitacaoTitle">
        <div class="custom-modal-backdrop" onclick="fecharModalSolicitacao()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h5 id="modalSolicitacaoTitle">Nova solicitação de abastecimento</h5>
                    <p>Preencha os dados abaixo para enviar sua solicitação para análise.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="fecharModalSolicitacao()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form action="{{ route('abastecimento.solicitacoes.store') }}" method="POST" id="formSolicitacao">
                    @csrf

                    <input type="hidden" name="foto_painel_base64" id="foto_painel_base64" value="{{ old('foto_painel_base64') }}">
                    <input type="hidden" name="foto_painel_nome" id="foto_painel_nome" value="{{ old('foto_painel_nome') }}">
                    <input type="hidden" name="foto_painel_mime" id="foto_painel_mime" value="{{ old('foto_painel_mime', 'image/jpeg') }}">

                    <div class="form-grid">
                        <div class="form-group full">
    <label class="form-label">Veículo</label>
    <select name="veiculo_id" class="form-select-custom" required>
        <option value="">Selecione o veículo</option>

        @foreach ($veiculos as $veiculoOption)
            <option
                value="{{ $veiculoOption->id }}"
                {{ old('veiculo_id') == $veiculoOption->id ? 'selected' : '' }}
            >
                {{ $veiculoOption->placa }}
                —
                {{ trim(($veiculoOption->marca ?? '') . ' ' . ($veiculoOption->modelo ?? '')) }}
                @if ($veiculoOption->ano)
                    / {{ $veiculoOption->ano }}
                @endif
                —
                KM: {{ number_format((float) $veiculoOption->km_atual, 1, ',', '.') }}
            </option>
        @endforeach
    </select>
</div>
                        <div class="form-group">
                            <label class="form-label">Data da solicitação</label>
                            <input
                                type="date"
                                name="data_solicitacao"
                                class="form-control-custom"
                                value="{{ old('data_solicitacao', now()->format('Y-m-d')) }}"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">KM informado</label>
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                name="km_informado"
                                class="form-control-custom"
                                value="{{ old('km_informado') }}"
                                required
                            >
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Foto do painel com KM</label>

                            <div class="camera-actions">
                                <button type="button" class="btn btn-dark-primary" onclick="abrirCameraPainel()">
                                    <i class="bi bi-camera"></i>
                                    Tirar foto do painel
                                </button>

                                <button type="button" class="btn btn-secondary-dark" onclick="limparFotoPainel()">
                                    <i class="bi bi-trash"></i>
                                    Remover foto
                                </button>
                            </div>

                            <span class="form-help">
                                Tire uma foto nítida do painel mostrando o KM. No celular, você poderá alternar entre câmera frontal e traseira.
                            </span>

                            <div class="preview-box {{ old('foto_painel_base64') ? 'active' : '' }}" id="previewFotoPainelBox">
                                <img
                                    id="previewFotoPainelImg"
                                    src="{{ old('foto_painel_base64') ?: '' }}"
                                    alt="Prévia da foto do painel"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo da solicitação</label>
                            <select name="tipo_solicitacao" class="form-select-custom" required>
                                <option value="">Selecione</option>
                                <option value="valor" {{ old('tipo_solicitacao') === 'valor' ? 'selected' : '' }}>Valor</option>
                                <option value="litros" {{ old('tipo_solicitacao') === 'litros' ? 'selected' : '' }}>Litros</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quantidade solicitada</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                name="quantidade_solicitada"
                                class="form-control-custom"
                                value="{{ old('quantidade_solicitada') }}"
                                required
                            >
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Observação</label>
                            <textarea
                                name="observacao_usuario"
                                class="form-control-custom"
                                placeholder="Ex.: abastecimento para viagem, deslocamento para obra, visita técnica..."
                            >{{ old('observacao_usuario') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary-dark" onclick="fecharModalSolicitacao()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-dark-primary" id="btnSubmitSolicitacao">
                            <i class="bi bi-check-circle"></i> Enviar solicitação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="custom-modal" id="modalCameraPainel" role="dialog" aria-modal="true" aria-labelledby="modalCameraPainelTitle">
        <div class="custom-modal-backdrop" onclick="fecharCameraPainel()"></div>

        <div class="custom-modal-dialog" style="max-width: 680px;">
            <div class="custom-modal-header">
                <div>
                    <h5 id="modalCameraPainelTitle">Capturar foto do painel</h5>
                    <p>Posicione o painel do veículo no centro da imagem. Você pode trocar a câmera se necessário.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="fecharCameraPainel()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <div class="camera-box">
                    <video id="cameraPainelPreview" autoplay playsinline muted></video>
                    <canvas id="cameraPainelCanvas" style="display:none;"></canvas>
                    <div class="camera-status" id="cameraPainelStatus">
                        Aguardando câmera...
                    </div>
                </div>

                <div class="camera-actions">
                    <button type="button" class="btn btn-secondary-dark" onclick="trocarCameraPainel()">
                        <i class="bi bi-arrow-repeat"></i>
                        Trocar câmera
                    </button>

                    <button type="button" class="btn btn-dark-primary" onclick="capturarFotoPainel()">
                        <i class="bi bi-camera-fill"></i>
                        Capturar foto
                    </button>

                    <button type="button" class="btn btn-secondary-dark" onclick="fecharCameraPainel()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-modal" id="modalComprovante" role="dialog" aria-modal="true" aria-labelledby="modalComprovanteTitle">
        <div class="custom-modal-backdrop" onclick="fecharModalComprovante()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h5 id="modalComprovanteTitle">Enviar comprovante de abastecimento</h5>
                    <p>Envie a foto da nota e uma selfie. Apenas fotos tiradas pela câmera são aceitas.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="fecharModalComprovante()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form method="POST" id="formComprovante">
                    @csrf

                    <input type="hidden" name="foto_nota_base64" id="foto_nota_base64" value="{{ old('foto_nota_base64') }}">
                    <input type="hidden" name="foto_nota_nome" id="foto_nota_nome" value="{{ old('foto_nota_nome') }}">
                    <input type="hidden" name="foto_nota_mime" id="foto_nota_mime" value="{{ old('foto_nota_mime', 'image/jpeg') }}">

                    <input type="hidden" name="foto_selfie_base64" id="foto_selfie_base64" value="{{ old('foto_selfie_base64') }}">
                    <input type="hidden" name="foto_selfie_nome" id="foto_selfie_nome" value="{{ old('foto_selfie_nome') }}">
                    <input type="hidden" name="foto_selfie_mime" id="foto_selfie_mime" value="{{ old('foto_selfie_mime', 'image/jpeg') }}">

                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label">Foto da nota</label>

                            <div class="camera-actions">
                                <button type="button" class="btn btn-dark-primary" onclick="abrirCameraComprovante('nota')">
                                    <i class="bi bi-receipt"></i>
                                    Tirar foto da nota
                                </button>

                                <button type="button" class="btn btn-secondary-dark" onclick="limparFotoComprovante('nota')">
                                    <i class="bi bi-trash"></i>
                                    Remover foto
                                </button>
                            </div>

                            <span class="form-help">
                                Tire uma foto nítida da nota ou cupom do abastecimento.
                            </span>

                            <div class="preview-box {{ old('foto_nota_base64') ? 'active' : '' }}" id="previewNotaBox">
                                <img id="previewNotaImg" src="{{ old('foto_nota_base64') ?: '' }}" alt="Prévia da nota">
                            </div>
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Selfie do usuário</label>

                            <div class="camera-actions">
                                <button type="button" class="btn btn-dark-primary" onclick="abrirCameraComprovante('selfie')">
                                    <i class="bi bi-person-circle"></i>
                                    Tirar selfie
                                </button>

                                <button type="button" class="btn btn-secondary-dark" onclick="limparFotoComprovante('selfie')">
                                    <i class="bi bi-trash"></i>
                                    Remover selfie
                                </button>
                            </div>

                            <span class="form-help">
                                Tire uma selfie no momento do envio do comprovante.
                            </span>

                            <div class="preview-box {{ old('foto_selfie_base64') ? 'active' : '' }}" id="previewSelfieBox">
                                <img id="previewSelfieImg" src="{{ old('foto_selfie_base64') ?: '' }}" alt="Prévia da selfie">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary-dark" onclick="fecharModalComprovante()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-dark-primary" id="btnSubmitComprovante">
                            <i class="bi bi-check-circle"></i> Enviar comprovante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="custom-modal" id="modalCameraComprovante" role="dialog" aria-modal="true" aria-labelledby="modalCameraComprovanteTitle">
        <div class="custom-modal-backdrop" onclick="fecharCameraComprovante()"></div>

        <div class="custom-modal-dialog" style="max-width: 680px;">
            <div class="custom-modal-header">
                <div>
                    <h5 id="modalCameraComprovanteTitle">Capturar foto</h5>
                    <p>Tire a foto e confirme para continuar.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="fecharCameraComprovante()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <div class="camera-box">
                    <video id="cameraComprovantePreview" autoplay playsinline muted></video>
                    <canvas id="cameraComprovanteCanvas" style="display:none;"></canvas>
                    <div class="camera-status" id="cameraComprovanteStatus">
                        Aguardando câmera...
                    </div>
                </div>

                <div class="camera-actions">
                    <button type="button" class="btn btn-secondary-dark" onclick="trocarCameraComprovante()">
                        <i class="bi bi-arrow-repeat"></i>
                        Trocar câmera
                    </button>

                    <button type="button" class="btn btn-dark-primary" onclick="capturarFotoComprovante()">
                        <i class="bi bi-camera-fill"></i>
                        Capturar foto
                    </button>

                    <button type="button" class="btn btn-secondary-dark" onclick="fecharCameraComprovante()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
    let cameraPainelStream = null;
    let cameraPainelAtual = 'environment';

    let cameraComprovanteStream = null;
    let cameraComprovanteAtual = 'environment';
    let tipoCapturaAtual = null;
    let solicitacaoComprovanteAtual = null;

    function abrirModalSolicitacao() {
        const modal = document.getElementById('modalSolicitacao');

        if (modal) {
            modal.classList.add('is-open');
            document.body.classList.add('modal-open');

            const firstInput = modal.querySelector('input, select, textarea, button');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 50);
            }
        }
    }

    function fecharModalSolicitacao() {
        const modal = document.getElementById('modalSolicitacao');

        if (modal) {
            modal.classList.remove('is-open');
        }

        fecharCameraPainel(false);

        if (!document.querySelector('.custom-modal.is-open')) {
            document.body.classList.remove('modal-open');
        }
    }

    async function abrirCameraPainel() {
        const modal = document.getElementById('modalCameraPainel');

        if (!modal) return;

        modal.classList.add('is-open');
        document.body.classList.add('modal-open');

        await iniciarCameraPainel(cameraPainelAtual);
    }

    async function iniciarCameraPainel(facingMode = 'environment') {
        const status = document.getElementById('cameraPainelStatus');
        const video = document.getElementById('cameraPainelPreview');

        try {
            fecharCameraPainel(false);

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Seu navegador não suporta acesso à câmera.');
                return;
            }

            status.textContent = 'Abrindo câmera...';

            cameraPainelStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: facingMode }
                },
                audio: false
            });

            cameraPainelAtual = facingMode;
            video.srcObject = cameraPainelStream;

            if (cameraPainelAtual === 'user') {
                video.style.transform = 'scaleX(-1)';
            } else {
                video.style.transform = 'scaleX(1)';
            }

            status.textContent = cameraPainelAtual === 'user'
                ? 'Câmera frontal ativa.'
                : 'Câmera traseira ativa.';
        } catch (error) {
            console.error(error);
            status.textContent = 'Não foi possível acessar a câmera.';
            alert('Não foi possível acessar a câmera. Verifique a permissão do navegador. Em celular, normalmente funciona melhor em HTTPS.');
        }
    }

    async function trocarCameraPainel() {
        cameraPainelAtual = cameraPainelAtual === 'environment' ? 'user' : 'environment';
        await iniciarCameraPainel(cameraPainelAtual);
    }

    function fecharCameraPainel(fecharModal = true) {
        const modal = document.getElementById('modalCameraPainel');
        const video = document.getElementById('cameraPainelPreview');
        const status = document.getElementById('cameraPainelStatus');

        if (fecharModal && modal) {
            modal.classList.remove('is-open');
        }

        if (video) {
            video.srcObject = null;
        }

        if (cameraPainelStream) {
            cameraPainelStream.getTracks().forEach(track => track.stop());
            cameraPainelStream = null;
        }

        if (status) {
            status.textContent = 'Aguardando câmera...';
        }

        if (!document.querySelector('.custom-modal.is-open')) {
            document.body.classList.remove('modal-open');
        }
    }

    function capturarFotoPainel() {
        const video = document.getElementById('cameraPainelPreview');
        const canvas = document.getElementById('cameraPainelCanvas');

        if (!video || !video.videoWidth || !video.videoHeight) {
            alert('A câmera ainda não está pronta.');
            return;
        }

        const larguraMaxima = 1600;
        let largura = video.videoWidth;
        let altura = video.videoHeight;

        if (largura > larguraMaxima) {
            altura = Math.round((altura * larguraMaxima) / largura);
            largura = larguraMaxima;
        }

        canvas.width = largura;
        canvas.height = altura;

        const context = canvas.getContext('2d');

        if (cameraPainelAtual === 'user') {
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
        }

        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const base64 = canvas.toDataURL('image/jpeg', 0.88);

        document.getElementById('foto_painel_base64').value = base64;
        document.getElementById('foto_painel_nome').value = 'painel-' + Date.now() + '.jpg';
        document.getElementById('foto_painel_mime').value = 'image/jpeg';

        const previewBox = document.getElementById('previewFotoPainelBox');
        const previewImg = document.getElementById('previewFotoPainelImg');

        previewImg.src = base64;
        previewBox.classList.add('active');

        fecharCameraPainel();
    }

    function limparFotoPainel() {
        document.getElementById('foto_painel_base64').value = '';
        document.getElementById('foto_painel_nome').value = '';
        document.getElementById('foto_painel_mime').value = 'image/jpeg';

        const previewBox = document.getElementById('previewFotoPainelBox');
        const previewImg = document.getElementById('previewFotoPainelImg');

        previewImg.src = '';
        previewBox.classList.remove('active');
    }

    function abrirModalComprovante(solicitacaoId) {
        solicitacaoComprovanteAtual = solicitacaoId;

        const modal = document.getElementById('modalComprovante');
        const form = document.getElementById('formComprovante');

        form.action = "{{ url('/abastecimento/minhas-solicitacoes') }}/" + solicitacaoId + "/comprovante";

        limparFotoComprovante('nota');
        limparFotoComprovante('selfie');

        modal.classList.add('is-open');
        document.body.classList.add('modal-open');
    }

    function fecharModalComprovante() {
        const modal = document.getElementById('modalComprovante');

        if (modal) {
            modal.classList.remove('is-open');
        }

        fecharCameraComprovante(false);

        if (!document.querySelector('.custom-modal.is-open')) {
            document.body.classList.remove('modal-open');
        }
    }

    async function abrirCameraComprovante(tipo) {
        tipoCapturaAtual = tipo;

        if (tipo === 'selfie') {
            cameraComprovanteAtual = 'user';
        } else {
            cameraComprovanteAtual = 'environment';
        }

        const modal = document.getElementById('modalCameraComprovante');
        modal.classList.add('is-open');
        document.body.classList.add('modal-open');

        await iniciarCameraComprovante(cameraComprovanteAtual);
    }

    async function iniciarCameraComprovante(facingMode = 'environment') {
        const status = document.getElementById('cameraComprovanteStatus');
        const video = document.getElementById('cameraComprovantePreview');

        try {
            fecharCameraComprovante(false);

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Seu navegador não suporta acesso à câmera.');
                return;
            }

            status.textContent = 'Abrindo câmera...';

            cameraComprovanteStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: facingMode }
                },
                audio: false
            });

            cameraComprovanteAtual = facingMode;
            video.srcObject = cameraComprovanteStream;

            if (cameraComprovanteAtual === 'user') {
                video.style.transform = 'scaleX(-1)';
                status.textContent = 'Câmera frontal ativa.';
            } else {
                video.style.transform = 'scaleX(1)';
                status.textContent = 'Câmera traseira ativa.';
            }
        } catch (error) {
            console.error(error);
            status.textContent = 'Não foi possível acessar a câmera.';
            alert('Não foi possível acessar a câmera. Verifique a permissão do navegador. Em celular, normalmente funciona melhor em HTTPS.');
        }
    }

    async function trocarCameraComprovante() {
        cameraComprovanteAtual = cameraComprovanteAtual === 'environment' ? 'user' : 'environment';
        await iniciarCameraComprovante(cameraComprovanteAtual);
    }

    function fecharCameraComprovante(fecharModal = true) {
        const modal = document.getElementById('modalCameraComprovante');
        const video = document.getElementById('cameraComprovantePreview');
        const status = document.getElementById('cameraComprovanteStatus');

        if (fecharModal && modal) {
            modal.classList.remove('is-open');
        }

        if (video) {
            video.srcObject = null;
        }

        if (cameraComprovanteStream) {
            cameraComprovanteStream.getTracks().forEach(track => track.stop());
            cameraComprovanteStream = null;
        }

        if (status) {
            status.textContent = 'Aguardando câmera...';
        }

        if (!document.querySelector('.custom-modal.is-open')) {
            document.body.classList.remove('modal-open');
        }
    }

    function capturarFotoComprovante() {
        const video = document.getElementById('cameraComprovantePreview');
        const canvas = document.getElementById('cameraComprovanteCanvas');

        if (!video || !video.videoWidth || !video.videoHeight) {
            alert('A câmera ainda não está pronta.');
            return;
        }

        const larguraMaxima = 1600;
        let largura = video.videoWidth;
        let altura = video.videoHeight;

        if (largura > larguraMaxima) {
            altura = Math.round((altura * larguraMaxima) / largura);
            largura = larguraMaxima;
        }

        canvas.width = largura;
        canvas.height = altura;

        const context = canvas.getContext('2d');

        if (cameraComprovanteAtual === 'user') {
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
        }

        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const base64 = canvas.toDataURL('image/jpeg', 0.88);

        if (tipoCapturaAtual === 'nota') {
            document.getElementById('foto_nota_base64').value = base64;
            document.getElementById('foto_nota_nome').value = 'nota-' + Date.now() + '.jpg';
            document.getElementById('foto_nota_mime').value = 'image/jpeg';
            document.getElementById('previewNotaImg').src = base64;
            document.getElementById('previewNotaBox').classList.add('active');
        }

        if (tipoCapturaAtual === 'selfie') {
            document.getElementById('foto_selfie_base64').value = base64;
            document.getElementById('foto_selfie_nome').value = 'selfie-' + Date.now() + '.jpg';
            document.getElementById('foto_selfie_mime').value = 'image/jpeg';
            document.getElementById('previewSelfieImg').src = base64;
            document.getElementById('previewSelfieBox').classList.add('active');
        }

        fecharCameraComprovante();
    }

    function limparFotoComprovante(tipo) {
        if (tipo === 'nota') {
            document.getElementById('foto_nota_base64').value = '';
            document.getElementById('foto_nota_nome').value = '';
            document.getElementById('foto_nota_mime').value = 'image/jpeg';
            document.getElementById('previewNotaImg').src = '';
            document.getElementById('previewNotaBox').classList.remove('active');
        }

        if (tipo === 'selfie') {
            document.getElementById('foto_selfie_base64').value = '';
            document.getElementById('foto_selfie_nome').value = '';
            document.getElementById('foto_selfie_mime').value = 'image/jpeg';
            document.getElementById('previewSelfieImg').src = '';
            document.getElementById('previewSelfieBox').classList.remove('active');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('modalCameraComprovante')?.classList.contains('is-open')) {
                fecharCameraComprovante();
                return;
            }

            if (document.getElementById('modalCameraPainel')?.classList.contains('is-open')) {
                fecharCameraPainel();
                return;
            }

            if (document.getElementById('modalComprovante')?.classList.contains('is-open')) {
                fecharModalComprovante();
                return;
            }

            fecharModalSolicitacao();
        }
    });

    document.getElementById('formSolicitacao')?.addEventListener('submit', function(e) {
        const fotoBase64 = document.getElementById('foto_painel_base64')?.value;

        if (!fotoBase64) {
            e.preventDefault();
            alert('Tire a foto do painel antes de enviar a solicitação.');
            return;
        }

        const btn = document.getElementById('btnSubmitSolicitacao');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
        }
    });

    document.getElementById('formComprovante')?.addEventListener('submit', function(e) {
        const nota = document.getElementById('foto_nota_base64')?.value;
        const selfie = document.getElementById('foto_selfie_base64')?.value;

        if (!nota) {
            e.preventDefault();
            alert('Tire a foto da nota antes de enviar.');
            return;
        }

        if (!selfie) {
            e.preventDefault();
            alert('Tire a selfie antes de enviar.');
            return;
        }

        const btn = document.getElementById('btnSubmitComprovante');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
        }
    });

    @if(session('open_create_modal'))
        document.addEventListener('DOMContentLoaded', function () {
            abrirModalSolicitacao();
        });
    @endif

    @if(session('open_comprovante_modal'))
        document.addEventListener('DOMContentLoaded', function () {
            abrirModalComprovante({{ session('open_comprovante_modal') }});

            @if(old('foto_nota_base64'))
                document.getElementById('previewNotaBox')?.classList.add('active');
                document.getElementById('previewNotaImg').src = @json(old('foto_nota_base64'));
            @endif

            @if(old('foto_selfie_base64'))
                document.getElementById('previewSelfieBox')?.classList.add('active');
                document.getElementById('previewSelfieImg').src = @json(old('foto_selfie_base64'));
            @endif
        });
    @endif

    window.addEventListener('beforeunload', function () {
        if (cameraPainelStream) {
            cameraPainelStream.getTracks().forEach(track => track.stop());
        }

        if (cameraComprovanteStream) {
            cameraComprovanteStream.getTracks().forEach(track => track.stop());
        }
    });
</script>
@endsection