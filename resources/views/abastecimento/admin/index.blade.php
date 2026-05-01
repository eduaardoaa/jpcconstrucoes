@extends('layouts.app')

@section('title', 'Solicitações de Combustível')
@section('pageTitle', 'Solicitações de Combustível')
@section('pageDescription', 'Gerencie aprovações, reprovações e ajustes das solicitações.')

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

        .filters-form {
            display: grid;
            grid-template-columns: 1.5fr 220px 220px 220px 220px auto;
            gap: 12px;
            align-items: end;
            margin-bottom: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            color: #cbd5e1;
            font-size: .85rem;
            font-weight: 600;
        }

        .form-control-custom,
        .form-select-custom,
        .form-control-custom:focus,
        .form-select-custom:focus {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, .16);
            background: rgba(15, 23, 42, .95);
            color: #f8fafc;
            padding: 12px 14px;
            outline: none;
            box-shadow: none;
        }

        .btn-dark-primary,
.btn-secondary-dark,
.btn-action {
    border: none;
    border-radius: 14px;
    padding: 10px 16px;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
}

        .btn-dark-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .btn-secondary-dark {
            background: rgba(51, 65, 85, .95);
        }

        .btn-action {
            background: rgba(30, 41, 59, .85);
            border: 1px solid rgba(148, 163, 184, .16);
            color: #e2e8f0;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: .88rem;
        }

        .btn-success-soft {
            background: rgba(20, 83, 45, .24);
            color: #bbf7d0;
            border: 1px solid rgba(74, 222, 128, .15);
        }

        .btn-danger-soft {
            background: rgba(127, 29, 29, .24);
            color: #fecaca;
            border: 1px solid rgba(248, 113, 113, .15);
        }

        .btn-info-soft {
            background: rgba(30, 64, 175, .24);
            color: #bfdbfe;
            border: 1px solid rgba(96, 165, 250, .15);
        }

        .btn-anexo-soft {
            background: rgba(30, 41, 59, .85);
            color: #e2e8f0;
            border: 1px solid rgba(148, 163, 184, .16);
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

        .actions-inline {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .thumb-foto {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, .16);
            display: block;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .18);
        }

        .foto-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .foto-vazia {
            color: #94a3b8;
            font-size: .88rem;
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

        .form-grid-modal {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        textarea.form-control-custom {
            min-height: 110px;
            resize: vertical;
        }

        .modal-footer-custom {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        .modal-open {
            overflow: hidden;
        }

        .comprovante-admin-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .comprovante-admin-card {
            background: rgba(15, 23, 42, .72);
            border: 1px solid rgba(148, 163, 184, .12);
            border-radius: 18px;
            padding: 14px;
        }

        .comprovante-admin-label {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .comprovante-admin-img {
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            border-radius: 14px;
            background: #020617;
            border: 1px solid rgba(148, 163, 184, .14);
        }

        .meta-text {
            color: #94a3b8;
            font-size: .85rem;
            line-height: 1.45;
        }

        @media (max-width: 1200px) {
            .filters-form {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        @media (max-width: 920px) {
            .filters-form,
            .form-grid-modal,
            .comprovante-admin-grid {
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
        }

        @media (max-width: 768px) {
            .table-dark-custom thead {
                display: none;
            }

            .table-dark-custom,
            .table-dark-custom tbody,
            .table-dark-custom tr,
            .table-dark-custom td {
                display: block;
                width: 100%;
            }

            .table-dark-custom tbody tr {
                margin-bottom: 14px;
                border-radius: 16px;
                padding: 10px;
            }

            .table-dark-custom tbody tr td:first-child,
            .table-dark-custom tbody tr td:last-child {
                border-radius: 0;
            }

            .table-dark-custom tbody td {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
                padding: 8px 10px;
                border-bottom: 1px solid rgba(148, 163, 184, .08);
            }

            .table-dark-custom tbody td:last-child {
                border-bottom: none;
            }

            .table-dark-custom tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #94a3b8;
                font-size: .78rem;
                flex-shrink: 0;
                min-width: 100px;
            }

            .table-dark-custom tbody td > * {
                text-align: right;
            }

            .table-dark-custom tbody td small,
            .table-dark-custom tbody td strong,
            .table-dark-custom tbody td span {
                display: block;
            }

            .table-dark-custom tbody td[data-label="Ações"] {
                display: block;
            }

            .table-dark-custom tbody td[data-label="Ações"]::before {
                display: block;
                margin-bottom: 8px;
            }

            .table-dark-custom tbody td[data-label="Ações"] .actions-inline {
                justify-content: stretch;
                display: grid;
                grid-template-columns: 1fr;
            }

            .table-dark-custom tbody td[data-label="Ações"] .actions-inline button {
                width: 100%;
            }

            .actions-inline {
                justify-content: flex-end;
            }

            .actions-inline button {
                flex: 1;
            }

            .thumb-foto {
                margin-left: auto;
            }
        }
    </style>
@php
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
            <h2>Solicitações de combustível</h2>
            <p>Analise, aprove, reprove ou ajuste as solicitações dos usuários.</p>
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

    <div class="dark-card">
        <div class="dark-card-header">
            <h3>Filtros</h3>
            <p>Refine a visualização das solicitações.</p>
        </div>
        <div class="dark-card-body">
            <form method="GET" action="{{ route('abastecimento.admin.index') }}" class="filters-form">
                <div class="form-group">
                    <label class="form-label">Buscar</label>
                    <input
                        type="text"
                        name="busca"
                        class="form-control-custom"
                        value="{{ $busca }}"
                        placeholder="Usuário, email, placa, marca..."
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select-custom">
                        <option value="">Todos</option>
                        <option value="pendente" {{ $status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="aprovada" {{ $status === 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                        <option value="reprovada" {{ $status === 'reprovada' ? 'selected' : '' }}>Reprovada</option>
                        <option value="ajustada" {{ $status === 'ajustada' ? 'selected' : '' }}>Ajustada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select-custom">
                        <option value="">Todos</option>
                        <option value="valor" {{ $tipo === 'valor' ? 'selected' : '' }}>Valor</option>
                        <option value="litros" {{ $tipo === 'litros' ? 'selected' : '' }}>Litros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Data início</label>
                    <input
                        type="date"
                        name="data_inicio"
                        class="form-control-custom"
                        value="{{ $dataInicio ?? '' }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Data fim</label>
                    <input
                        type="date"
                        name="data_fim"
                        class="form-control-custom"
                        value="{{ $dataFim ?? '' }}"
                    >
                </div>

                <div class="actions-inline">
                    <button type="submit" class="btn-dark-primary">Filtrar</button>
                    <a href="{{ route('abastecimento.admin.index') }}" class="btn-secondary-dark" style="text-decoration:none;">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div style="height: 18px;"></div>

    <div class="dark-card">
        <div class="dark-card-header">
            <h3>Lista de solicitações</h3>
            <p>{{ $solicitacoes->count() }} solicitação(ões) encontrada(s).</p>
        </div>
        <div class="dark-card-body">
            <div class="table-wrap">
                <table class="table-dark-custom">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Veículo</th>
                            <th>Data</th>
                            <th>KM</th>
                            <th>Painel</th>
                            <th>Tipo</th>
                            <th>Solicitado</th>
                            <th>Aprovado</th>
                            <th>Status</th>
                            <th>Comprovante</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($solicitacoes as $solicitacao)
                            <tr>
                                <td data-label="Usuário">
                                    <div>
                                        <strong>{{ $solicitacao->usuario->name ?? '—' }}</strong><br>
                                        <small style="color:#94a3b8;">
                                            {{ $solicitacao->usuario->cargo->nome ?? ($solicitacao->usuario->email ?? '—') }}
                                        </small>
                                    </div>
                                </td>

                                <td data-label="Veículo">
                                    <div>
                                        @if ($solicitacao->veiculo)
                                            <strong>{{ $solicitacao->veiculo->placa }}</strong><br>
                                            <small style="color:#94a3b8;">
                                                {{ $solicitacao->veiculo->marca }} {{ $solicitacao->veiculo->modelo }}
                                            </small>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>

                                <td data-label="Data">
                                    <span>{{ optional($solicitacao->data_solicitacao)->format('d/m/Y') }}</span>
                                </td>

                                <td data-label="KM">
                                    <span>{{ number_format((float) $solicitacao->km_informado, 1, ',', '.') }}</span>
                                </td>

                                <td data-label="Painel">
                                    @if ($solicitacao->foto_painel)
                                        <a
                                            href="{{ $fotoUrl($solicitacao->foto_painel) }}"
                                            target="_blank"
                                            class="foto-link"
                                            title="Abrir foto do painel"
                                        >
                                            <img
                                                src="{{ $fotoUrl($solicitacao->foto_painel) }}"
                                                alt="Foto do painel"
                                                class="thumb-foto"
                                            >
                                        </a>
                                    @else
                                        <span class="foto-vazia">—</span>
                                    @endif
                                </td>

                                <td data-label="Tipo">
                                    <span>{{ strtoupper($solicitacao->tipo_solicitacao) }}</span>
                                </td>

                                <td data-label="Solicitado">
                                    <span>{{ number_format((float) $solicitacao->quantidade_solicitada, 2, ',', '.') }}</span>
                                </td>

                                <td data-label="Aprovado">
                                    <span>
                                        {{ $solicitacao->quantidade_aprovada !== null
                                            ? number_format((float) $solicitacao->quantidade_aprovada, 2, ',', '.')
                                            : '—' }}
                                    </span>
                                </td>

                                <td data-label="Status">
                                    <span class="badge-status {{ $solicitacao->status }}">
                                        {{ strtoupper($solicitacao->status) }}
                                    </span>
                                </td>

                                <td data-label="Comprovante">
                                    @if ($solicitacao->status_comprovante === 'enviado' && $solicitacao->foto_nota && $solicitacao->foto_selfie)
                                        <div class="actions-inline">
                                            <span class="badge-status enviado">ENVIADO</span>

                                           <button
    type="button"
    class="btn-action btn-anexo-soft btn-ver-anexo"
    data-url-nota="{{ $fotoUrl($solicitacao->foto_nota) }}"
    data-url-selfie="{{ $fotoUrl($solicitacao->foto_selfie) }}"
    data-usuario="{{ $solicitacao->usuario->name ?? 'Usuário' }}"
    data-data-envio="{{ $solicitacao->comprovante_enviado_em?->format('d/m/Y H:i') ?? '-' }}"
>
    <i class="bi bi-images"></i> Ver anexo
</button>
                                        </div>
                                    @else
                                        <span class="foto-vazia">—</span>
                                    @endif
                                </td>

                                <td data-label="Ações">
                                    @if ($solicitacao->status === 'pendente')
                                        <div class="actions-inline">
                                            <button
                                                type="button"
                                                class="btn-action btn-success-soft"
                                                onclick="abrirModalAcao(
                                                    'aprovar',
                                                    '{{ route('abastecimento.admin.aprovar', $solicitacao) }}',
                                                    '{{ $solicitacao->id }}',
                                                    '{{ $solicitacao->tipo_solicitacao }}',
                                                    '{{ $solicitacao->quantidade_solicitada }}'
                                                )"
                                            >
                                                Aprovar
                                            </button>

                                            <button
                                                type="button"
                                                class="btn-action btn-info-soft"
                                                onclick="abrirModalAcao(
                                                    'ajustar',
                                                    '{{ route('abastecimento.admin.ajustar', $solicitacao) }}',
                                                    '{{ $solicitacao->id }}',
                                                    '{{ $solicitacao->tipo_solicitacao }}',
                                                    '{{ $solicitacao->quantidade_solicitada }}'
                                                )"
                                            >
                                                Ajustar
                                            </button>

                                            <button
                                                type="button"
                                                class="btn-action btn-danger-soft"
                                                onclick="abrirModalAcao(
                                                    'reprovar',
                                                    '{{ route('abastecimento.admin.reprovar', $solicitacao) }}',
                                                    '{{ $solicitacao->id }}',
                                                    '{{ $solicitacao->tipo_solicitacao }}',
                                                    '{{ $solicitacao->quantidade_solicitada }}'
                                                )"
                                            >
                                                Reprovar
                                            </button>
                                        </div>
                                    @else
                                        <span style="color:#94a3b8;">Finalizada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="text-align:center; color:#94a3b8;">Nenhuma solicitação encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="custom-modal" id="modalAcaoSolicitacao" role="dialog" aria-modal="true" aria-labelledby="modalAcaoTitulo">
        <div class="custom-modal-backdrop" onclick="fecharModalAcao()"></div>

        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h5 id="modalAcaoTitulo">Ação da solicitação</h5>
                    <p>Revise os dados da solicitação e confirme a ação desejada.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="fecharModalAcao()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form id="formAcaoSolicitacao" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-grid-modal">
                        <div class="form-group">
                            <label class="form-label">Tipo</label>
                            <input type="text" id="modal_tipo_solicitacao" class="form-control-custom" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quantidade solicitada</label>
                            <input type="text" id="modal_quantidade_solicitada" class="form-control-custom" disabled>
                        </div>

                        <div class="form-group" id="grupo_quantidade_aprovada">
                            <label class="form-label">Quantidade aprovada/ajustada</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                name="quantidade_aprovada"
                                id="quantidade_aprovada"
                                class="form-control-custom"
                                value="{{ old('quantidade_aprovada') }}"
                            >
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Observação do admin</label>
                            <textarea name="observacao_admin" id="observacao_admin" class="form-control-custom">{{ old('observacao_admin') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-secondary-dark" onclick="fecharModalAcao()">Cancelar</button>
                        <button type="submit" class="btn-dark-primary" id="btnSalvarAcao">Salvar ação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="custom-modal" id="modalComprovanteAdmin" role="dialog" aria-modal="true" aria-labelledby="modalComprovanteAdminTitle">
        <div class="custom-modal-backdrop" onclick="fecharModalComprovanteAdmin()"></div>

        <div class="custom-modal-dialog" style="max-width: 900px;">
            <div class="custom-modal-header">
                <div>
                    <h5 id="modalComprovanteAdminTitle">Comprovante de abastecimento</h5>
                    <p id="modalComprovanteAdminInfo">Visualização do comprovante enviado pelo usuário.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="fecharModalComprovanteAdmin()" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <div class="comprovante-admin-grid">
                    <div class="comprovante-admin-card">
                        <div class="comprovante-admin-label">Foto da nota</div>

                        <a id="modalComprovanteAdminNotaLink" href="#" target="_blank">
                            <img id="modalComprovanteAdminNotaImg" src="" alt="Foto da nota" class="comprovante-admin-img">
                        </a>
                    </div>

                    <div class="comprovante-admin-card">
                        <div class="comprovante-admin-label">Selfie do usuário</div>

                        <a id="modalComprovanteAdminSelfieLink" href="#" target="_blank">
                            <img id="modalComprovanteAdminSelfieImg" src="" alt="Selfie do usuário" class="comprovante-admin-img">
                        </a>
                    </div>
                </div>

                <div style="margin-top: 14px;">
                    <div class="meta-text">
                        Clique na imagem para abrir em tamanho maior.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function abrirModalAcao(acao, actionUrl, id, tipo, quantidadeSolicitada) {
        const modal = document.getElementById('modalAcaoSolicitacao');
        const form = document.getElementById('formAcaoSolicitacao');
        const titulo = document.getElementById('modalAcaoTitulo');
        const grupoQuantidade = document.getElementById('grupo_quantidade_aprovada');
        const quantidadeAprovada = document.getElementById('quantidade_aprovada');
        const observacao = document.getElementById('observacao_admin');
        const btnSalvar = document.getElementById('btnSalvarAcao');

        form.action = actionUrl;
        document.getElementById('modal_tipo_solicitacao').value = (tipo || '').toUpperCase();
        document.getElementById('modal_quantidade_solicitada').value = quantidadeSolicitada ?? '';

        quantidadeAprovada.value = quantidadeSolicitada ?? '';
        observacao.value = '';

        if (acao === 'aprovar') {
            titulo.textContent = 'Aprovar solicitação';
            grupoQuantidade.style.display = 'flex';
            quantidadeAprovada.required = true;
            observacao.required = false;
            btnSalvar.textContent = 'Aprovar';
        } else if (acao === 'ajustar') {
            titulo.textContent = 'Ajustar solicitação';
            grupoQuantidade.style.display = 'flex';
            quantidadeAprovada.required = true;
            observacao.required = true;
            btnSalvar.textContent = 'Ajustar';
        } else {
            titulo.textContent = 'Reprovar solicitação';
            grupoQuantidade.style.display = 'none';
            quantidadeAprovada.required = false;
            observacao.required = true;
            btnSalvar.textContent = 'Reprovar';
        }

        modal.classList.add('is-open');
        document.body.classList.add('modal-open');

        const firstInput = modal.querySelector('input, select, textarea, button');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 50);
        }
    }

    function fecharModalAcao() {
        const modal = document.getElementById('modalAcaoSolicitacao');

        if (modal) {
            modal.classList.remove('is-open');
        }

        if (!document.querySelector('.custom-modal.is-open')) {
            document.body.classList.remove('modal-open');
        }
    }

    function abrirModalComprovanteAdmin(urlNota, urlSelfie, nomeUsuario, dataEnvio) {
        const modal = document.getElementById('modalComprovanteAdmin');

        document.getElementById('modalComprovanteAdminNotaImg').src = urlNota;
        document.getElementById('modalComprovanteAdminNotaLink').href = urlNota;

        document.getElementById('modalComprovanteAdminSelfieImg').src = urlSelfie;
        document.getElementById('modalComprovanteAdminSelfieLink').href = urlSelfie;

        document.getElementById('modalComprovanteAdminInfo').textContent =
            'Enviado por ' + nomeUsuario + ' em ' + dataEnvio + '.';

        modal.classList.add('is-open');
        document.body.classList.add('modal-open');
    }

    function fecharModalComprovanteAdmin() {
        const modal = document.getElementById('modalComprovanteAdmin');

        if (modal) {
            modal.classList.remove('is-open');
        }

        if (!document.querySelector('.custom-modal.is-open')) {
            document.body.classList.remove('modal-open');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-ver-anexo').forEach(function (botao) {
            botao.addEventListener('click', function () {
                abrirModalComprovanteAdmin(
                    this.dataset.urlNota,
                    this.dataset.urlSelfie,
                    this.dataset.usuario,
                    this.dataset.dataEnvio
                );
            });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('modalComprovanteAdmin')?.classList.contains('is-open')) {
                fecharModalComprovanteAdmin();
                return;
            }

            fecharModalAcao();
        }
    });

    @if(session('open_modal'))
        document.addEventListener('DOMContentLoaded', function () {
            const modalData = @json(session('open_modal'));

            abrirModalAcao(
                modalData.acao,
                modalData.rota,
                modalData.id,
                modalData.tipo,
                modalData.quantidade
            );

            @if(old('quantidade_aprovada') !== null)
                document.getElementById('quantidade_aprovada').value = @json(old('quantidade_aprovada'));
            @endif

            @if(old('observacao_admin') !== null)
                document.getElementById('observacao_admin').value = @json(old('observacao_admin'));
            @endif
        });
    @endif
</script>
@endsection