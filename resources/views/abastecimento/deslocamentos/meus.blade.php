@extends('layouts.app')

@section('title', 'Meus Deslocamentos')
@section('pageTitle', 'Meus Deslocamentos')
@section('pageDescription', 'Registre saídas, paradas e chegadas escolhendo um veículo disponível.')

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
            cursor: pointer;
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

        .btn-secondary-dark {
            border: 1px solid rgba(148, 163, 184, .24);
            background: rgba(15, 23, 42, 0.72);
            color: #e2e8f0;
            border-radius: 11px;
            padding: 10px 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-secondary-dark:hover {
            color: #fff;
            background: rgba(30, 41, 59, 0.92);
        }

        .btn-action {
            border: none;
            border-radius: 12px;
            padding: 8px 12px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
        }

        .btn-info-soft {
            background: rgba(30, 64, 175, .24);
            color: #bfdbfe;
            border: 1px solid rgba(96, 165, 250, .15);
        }

        .btn-success-soft {
            background: rgba(20, 83, 45, .24);
            color: #bbf7d0;
            border: 1px solid rgba(74, 222, 128, .15);
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
            grid-template-columns: repeat(4, 1fr);
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

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 700;
        }

        .badge-status.em_andamento {
            background: rgba(245, 158, 11, .14);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, .16);
        }

        .badge-status.finalizado {
            background: rgba(16, 185, 129, .15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, .2);
        }

        .deslocamento-card {
            background: rgba(15, 23, 42, .92);
            border: 1px solid rgba(148, 163, 184, .10);
            border-radius: 18px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 14px;
        }

        .deslocamento-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .deslocamento-title {
            color: #f8fafc;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .deslocamento-subtitle {
            color: #94a3b8;
            font-size: .88rem;
        }

        .actions-inline {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 14px;
            align-items: start;
            padding: 14px;
            border-radius: 16px;
            background: rgba(2, 6, 23, .35);
            border: 1px solid rgba(148, 163, 184, .10);
        }

        .timeline-type {
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #93c5fd;
        }

        .timeline-content strong {
            display: block;
            color: #f8fafc;
            margin-bottom: 4px;
        }

        .timeline-content span {
            display: block;
            color: #94a3b8;
            font-size: .88rem;
            margin-bottom: 3px;
        }

        .thumb-foto {
            width: 74px;
            height: 74px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, .16);
            display: block;
            margin-top: 6px;
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
            color: #94a3b8;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .form-control-custom {
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
        }

        .form-control-custom::placeholder {
            color: #64748b;
        }

        textarea.form-control-custom {
            min-height: 110px;
            resize: vertical;
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

        .gps-maps-link {
            color: #93c5fd;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .88rem;
        }

        .gps-maps-link:hover {
            color: #bfdbfe;
            text-decoration: underline;
        }

        @media (max-width: 920px) {
            .grid-top,
            .form-grid,
            .mini-stats,
            .timeline-item {
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
    </style>

    @php
        $temVeiculoAtivo = $veiculos->isNotEmpty();
        $total = $deslocamentos->count();
        $emAndamento = $deslocamentos->where('status', 'em_andamento')->count();
        $finalizados = $deslocamentos->where('status', 'finalizado')->count();
        $etapasTotal = $deslocamentos->sum(fn($d) => $d->etapas->count());
    @endphp

    <div class="page-head">
        <div>
            <h2>Meus deslocamentos</h2>
            <p>Registre saídas, paradas e chegadas escolhendo o veículo desejado.</p>
        </div>

        <div class="page-actions">
            @if ($temVeiculoAtivo)
                <button type="button" class="btn btn-dark-primary" onclick="abrirModalSaida()">
                    <i class="bi bi-plus-circle"></i> Nova saída
                </button>
            @else
                <button type="button" class="btn btn-dark-primary" disabled>
                    <i class="bi bi-plus-circle"></i> Nova saída
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
            Nenhum veículo ativo cadastrado. Cadastre ou ative um veículo para registrar deslocamentos.
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
                <p>Escolha o veículo no momento de registrar a saída.</p>
            </div>
            <div class="dark-card-body">
                <div class="vehicle-highlight">
                    <strong>{{ $veiculos->count() }} veículo(s) ativo(s)</strong>
                    <span>A saída não depende mais de veículo vinculado ao usuário.</span>
                    <span>Selecione o veículo desejado dentro do formulário.</span>
                </div>
            </div>
        </div>

        <div class="dark-card">
            <div class="dark-card-header">
                <h3>Resumo</h3>
                <p>Visão rápida dos seus registros.</p>
            </div>
            <div class="dark-card-body">
                <div class="mini-stats">
                    <div class="mini-stat">
                        <span class="mini-stat-label">Total</span>
                        <span class="mini-stat-value">{{ $total }}</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Em andamento</span>
                        <span class="mini-stat-value">{{ $emAndamento }}</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Finalizados</span>
                        <span class="mini-stat-value">{{ $finalizados }}</span>
                    </div>
                    <div class="mini-stat">
                        <span class="mini-stat-label">Etapas registradas</span>
                        <span class="mini-stat-value">{{ $etapasTotal }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dark-card">
        <div class="dark-card-header">
            <h3>Histórico</h3>
            <p>Acompanhe suas saídas, paradas e chegadas.</p>
        </div>

        <div class="dark-card-body">
            @if ($deslocamentos->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-sign-turn-right"></i>
                    Nenhum deslocamento registrado ainda.
                </div>
            @else
                @foreach ($deslocamentos as $deslocamento)
                    <div class="deslocamento-card">
                        <div class="deslocamento-top">
                            <div>
                                <div class="deslocamento-title">
                                    {{ $deslocamento->motivo ?: 'Deslocamento sem motivo informado' }}
                                </div>
                                <div class="deslocamento-subtitle">
                                    Veículo:
                                    {{ $deslocamento->veiculo->placa ?? '—' }}
                                    @if ($deslocamento->veiculo)
                                        —
                                        {{ trim(($deslocamento->veiculo->marca ?? '') . ' ' . ($deslocamento->veiculo->modelo ?? '')) }}
                                    @endif
                                    • Etapas: {{ $deslocamento->etapas->count() }}
                                </div>
                            </div>

                            <div class="actions-inline">
                                <span class="badge-status {{ $deslocamento->status }}">
                                    {{ strtoupper(str_replace('_', ' ', $deslocamento->status)) }}
                                </span>

                                @if ($deslocamento->status === 'em_andamento')
                                    <button type="button" class="btn-action btn-info-soft" onclick="abrirModalParada({{ $deslocamento->id }})">
                                        <i class="bi bi-geo-alt"></i> Parada
                                    </button>

                                    <button type="button" class="btn-action btn-success-soft" onclick="abrirModalChegada({{ $deslocamento->id }})">
                                        <i class="bi bi-flag"></i> Chegada
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="timeline">
                            @foreach ($deslocamento->etapas->sortBy('ordem') as $etapa)
                                <div class="timeline-item">
                                    <div class="timeline-type">
                                        {{ strtoupper($etapa->tipo_etapa) }}
                                    </div>

                                    <div class="timeline-content">
                                        <strong>{{ $etapa->local_etapa }}</strong>

                                        <span>
                                            Data: {{ optional($etapa->data_etapa)->format('d/m/Y') }}
                                            • Hora: {{ $etapa->hora_etapa }}
                                        </span>

                                        <span>
                                            KM: {{ number_format((float) $etapa->km_etapa, 1, ',', '.') }}
                                        </span>

                                        @if(!is_null($etapa->latitude) && !is_null($etapa->longitude))
                                            <span>
                                                <a class="gps-maps-link"
                                                   href="https://www.google.com/maps?q={{ $etapa->latitude }},{{ $etapa->longitude }}"
                                                   target="_blank"
                                                   rel="noopener"
                                                   data-lat="{{ $etapa->latitude }}"
                                                   data-lng="{{ $etapa->longitude }}">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                    <span class="gps-endereco-texto">Carregando endereço...</span>
                                                </a>
                                            </span>
                                        @endif

                                        @if($etapa->observacao)
                                            <span>Obs.: {{ $etapa->observacao }}</span>
                                        @endif

                                        @if ($etapa->foto_painel)
                                            <a href="{{ asset('storage/' . $etapa->foto_painel) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $etapa->foto_painel) }}" alt="Foto painel" class="thumb-foto">
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- MODAL SAÍDA --}}
    <div class="custom-modal" id="modalSaida">
        <div class="custom-modal-backdrop" onclick="fecharModal('modalSaida')"></div>
        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h5>Nova saída</h5>
                    <p>Registre o início do deslocamento.</p>
                </div>
                <button type="button" class="custom-modal-close" onclick="fecharModal('modalSaida')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form action="{{ route('deslocamentos.store') }}" method="POST" id="formSaida">
                    @csrf

                    <input type="hidden" name="foto_saida_base64" id="foto_saida_base64" value="{{ old('foto_saida_base64') }}">
                    <input type="hidden" name="foto_saida_nome" id="foto_saida_nome" value="{{ old('foto_saida_nome') }}">
                    <input type="hidden" name="foto_saida_mime" id="foto_saida_mime" value="{{ old('foto_saida_mime', 'image/jpeg') }}">
                    <input type="hidden" name="latitude_saida" id="latitude_saida" value="{{ old('latitude_saida') }}">
                    <input type="hidden" name="longitude_saida" id="longitude_saida" value="{{ old('longitude_saida') }}">
                    <input type="hidden" name="data_saida" id="data_saida" value="{{ old('data_saida') }}">
                    <input type="hidden" name="hora_saida" id="hora_saida" value="{{ old('hora_saida') }}">

                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label">Veículo</label>
                            <select name="veiculo_id" class="form-control-custom" required>
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

                        <div class="form-group full">
                            <label class="form-label">Local de saída</label>
                            <input type="text" name="local_saida" id="local_saida" class="form-control-custom" value="{{ old('local_saida') }}" placeholder="Digite o endereço de saída..." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">KM de saída</label>
                            <input type="number" step="0.1" min="0" name="km_saida" class="form-control-custom" value="{{ old('km_saida') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Motivo</label>
                            <input type="text" name="motivo" class="form-control-custom" value="{{ old('motivo') }}">
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Foto do painel</label>

                            <div class="camera-actions">
                                <button type="button" class="btn btn-dark-primary" onclick="abrirCameraEtapa('saida')">
                                    <i class="bi bi-camera"></i> Tirar foto
                                </button>

                                <button type="button" class="btn btn-secondary-dark" onclick="limparFotoEtapa('saida')">
                                    <i class="bi bi-trash"></i> Remover foto
                                </button>
                            </div>

                            <span class="form-help">Tire uma foto nítida do painel mostrando o KM.</span>

                            <div class="preview-box {{ old('foto_saida_base64') ? 'active' : '' }}" id="preview_saida_box">
                                <img id="preview_saida_img" src="{{ old('foto_saida_base64') ?: '' }}" alt="Prévia da saída">
                            </div>
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Observação</label>
                            <textarea name="observacao" class="form-control-custom">{{ old('observacao') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary-dark" onclick="fecharModal('modalSaida')">Cancelar</button>
                        <button type="submit" class="btn btn-dark-primary" id="btnSubmitSaida">Registrar saída</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL PARADA --}}
    <div class="custom-modal" id="modalParada">
        <div class="custom-modal-backdrop" onclick="fecharModal('modalParada')"></div>
        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h5>Nova parada</h5>
                    <p>Registre uma parada intermediária.</p>
                </div>
                <button type="button" class="custom-modal-close" onclick="fecharModal('modalParada')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form id="formParada" method="POST">
                    @csrf

                    <input type="hidden" name="foto_parada_base64" id="foto_parada_base64" value="{{ old('foto_parada_base64') }}">
                    <input type="hidden" name="foto_parada_nome" id="foto_parada_nome" value="{{ old('foto_parada_nome') }}">
                    <input type="hidden" name="foto_parada_mime" id="foto_parada_mime" value="{{ old('foto_parada_mime', 'image/jpeg') }}">
                    <input type="hidden" name="latitude_parada" id="latitude_parada" value="{{ old('latitude_parada') }}">
                    <input type="hidden" name="longitude_parada" id="longitude_parada" value="{{ old('longitude_parada') }}">
                    <input type="hidden" name="data_parada" id="data_parada" value="{{ old('data_parada') }}">
                    <input type="hidden" name="hora_parada" id="hora_parada" value="{{ old('hora_parada') }}">

                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label">Local da parada</label>
                            <input type="text" name="local_parada" id="local_parada" class="form-control-custom" value="{{ old('local_parada') }}" placeholder="Digite o endereço da parada..." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">KM da parada</label>
                            <input type="number" step="0.1" min="0" name="km_parada" class="form-control-custom" value="{{ old('km_parada') }}" required>
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Foto do painel</label>

                            <div class="camera-actions">
                                <button type="button" class="btn btn-dark-primary" onclick="abrirCameraEtapa('parada')">
                                    <i class="bi bi-camera"></i> Tirar foto
                                </button>

                                <button type="button" class="btn btn-secondary-dark" onclick="limparFotoEtapa('parada')">
                                    <i class="bi bi-trash"></i> Remover foto
                                </button>
                            </div>

                            <span class="form-help">Tire uma foto nítida do painel mostrando o KM.</span>

                            <div class="preview-box {{ old('foto_parada_base64') ? 'active' : '' }}" id="preview_parada_box">
                                <img id="preview_parada_img" src="{{ old('foto_parada_base64') ?: '' }}" alt="Prévia da parada">
                            </div>
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Observação</label>
                            <textarea name="observacao_parada" class="form-control-custom">{{ old('observacao_parada') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary-dark" onclick="fecharModal('modalParada')">Cancelar</button>
                        <button type="submit" class="btn btn-dark-primary" id="btnSubmitParada">Registrar parada</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CHEGADA --}}
    <div class="custom-modal" id="modalChegada">
        <div class="custom-modal-backdrop" onclick="fecharModal('modalChegada')"></div>
        <div class="custom-modal-dialog">
            <div class="custom-modal-header">
                <div>
                    <h5>Registrar chegada</h5>
                    <p>Finalize o deslocamento informando a chegada.</p>
                </div>
                <button type="button" class="custom-modal-close" onclick="fecharModal('modalChegada')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <form id="formChegada" method="POST">
                    @csrf

                    <input type="hidden" name="foto_chegada_base64" id="foto_chegada_base64" value="{{ old('foto_chegada_base64') }}">
                    <input type="hidden" name="foto_chegada_nome" id="foto_chegada_nome" value="{{ old('foto_chegada_nome') }}">
                    <input type="hidden" name="foto_chegada_mime" id="foto_chegada_mime" value="{{ old('foto_chegada_mime', 'image/jpeg') }}">
                    <input type="hidden" name="latitude_chegada" id="latitude_chegada" value="{{ old('latitude_chegada') }}">
                    <input type="hidden" name="longitude_chegada" id="longitude_chegada" value="{{ old('longitude_chegada') }}">
                    <input type="hidden" name="data_chegada" id="data_chegada" value="{{ old('data_chegada') }}">
                    <input type="hidden" name="hora_chegada" id="hora_chegada" value="{{ old('hora_chegada') }}">

                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label">Local da chegada</label>
                            <input type="text" name="local_chegada" id="local_chegada" class="form-control-custom" value="{{ old('local_chegada') }}" placeholder="Digite o endereço de chegada..." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">KM da chegada</label>
                            <input type="number" step="0.1" min="0" name="km_chegada" class="form-control-custom" value="{{ old('km_chegada') }}" required>
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Foto do painel</label>

                            <div class="camera-actions">
                                <button type="button" class="btn btn-dark-primary" onclick="abrirCameraEtapa('chegada')">
                                    <i class="bi bi-camera"></i> Tirar foto
                                </button>

                                <button type="button" class="btn btn-secondary-dark" onclick="limparFotoEtapa('chegada')">
                                    <i class="bi bi-trash"></i> Remover foto
                                </button>
                            </div>

                            <span class="form-help">Tire uma foto nítida do painel mostrando o KM.</span>

                            <div class="preview-box {{ old('foto_chegada_base64') ? 'active' : '' }}" id="preview_chegada_box">
                                <img id="preview_chegada_img" src="{{ old('foto_chegada_base64') ?: '' }}" alt="Prévia da chegada">
                            </div>
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Observação</label>
                            <textarea name="observacao_chegada" class="form-control-custom">{{ old('observacao_chegada') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary-dark" onclick="fecharModal('modalChegada')">Cancelar</button>
                        <button type="submit" class="btn btn-dark-primary" id="btnSubmitChegada">Finalizar deslocamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CÂMERA --}}
    <div class="custom-modal" id="modalCameraEtapa">
        <div class="custom-modal-backdrop" onclick="fecharCameraEtapa()"></div>
        <div class="custom-modal-dialog" style="max-width: 680px;">
            <div class="custom-modal-header">
                <div>
                    <h5>Capturar foto do painel</h5>
                    <p>Posicione o painel do veículo no centro da imagem.</p>
                </div>

                <button type="button" class="custom-modal-close" onclick="fecharCameraEtapa()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="custom-modal-body">
                <div class="camera-box">
                    <video id="cameraEtapaPreview" autoplay playsinline muted></video>
                    <canvas id="cameraEtapaCanvas" style="display:none;"></canvas>
                    <div class="camera-status" id="cameraEtapaStatus">Aguardando câmera...</div>
                </div>

                <div class="camera-actions">
                    <button type="button" class="btn btn-secondary-dark" onclick="trocarCameraEtapa()">
                        <i class="bi bi-arrow-repeat"></i> Trocar câmera
                    </button>

                    <button type="button" class="btn btn-dark-primary" onclick="capturarFotoEtapa()">
                        <i class="bi bi-camera-fill"></i> Capturar foto
                    </button>

                    <button type="button" class="btn btn-secondary-dark" onclick="fecharCameraEtapa()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const rotaParadaTemplate = @json(route('deslocamentos.parada.store', ['deslocamento' => '__ID__']));
        const rotaChegadaTemplate = @json(route('deslocamentos.chegada.store', ['deslocamento' => '__ID__']));

        let cameraEtapaStream = null;
        let cameraEtapaAtual = 'environment';
        let tipoEtapaAtual = null;

        function abrirModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.add('is-open');
            document.body.classList.add('modal-open');
        }

        function fecharModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.remove('is-open');

            if (!document.querySelector('.custom-modal.is-open')) {
                document.body.classList.remove('modal-open');
            }
        }

        function agoraLocal() {
            const agora = new Date();
            const tzOffset = agora.getTimezoneOffset() * 60000;
            const localISO = new Date(agora - tzOffset).toISOString();

            return {
                data: localISO.slice(0, 10),
                hora: localISO.slice(11, 16)
            };
        }

        function preencherDataHoraOculta(prefixo) {
            const atual = agoraLocal();
            const campoData = document.getElementById(`data_${prefixo}`);
            const campoHora = document.getElementById(`hora_${prefixo}`);

            if (campoData) campoData.value = atual.data;
            if (campoHora) campoHora.value = atual.hora;
        }

        async function capturarLocalizacaoEEndereco(prefixo) {
            if (!navigator.geolocation) return;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    const campoLat = document.getElementById(`latitude_${prefixo}`);
                    const campoLng = document.getElementById(`longitude_${prefixo}`);

                    if (campoLat) campoLat.value = lat;
                    if (campoLng) campoLng.value = lng;
                },
                function(error) {
                    console.error('Erro ao capturar localização:', error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function abrirModalSaida() {
            preencherDataHoraOculta('saida');
            capturarLocalizacaoEEndereco('saida');
            abrirModal('modalSaida');
        }

        function abrirModalParada(deslocamentoId) {
            const form = document.getElementById('formParada');
            if (form) {
                form.action = rotaParadaTemplate.replace('__ID__', deslocamentoId);
            }

            preencherDataHoraOculta('parada');
            capturarLocalizacaoEEndereco('parada');
            abrirModal('modalParada');
        }

        function abrirModalChegada(deslocamentoId) {
            const form = document.getElementById('formChegada');
            if (form) {
                form.action = rotaChegadaTemplate.replace('__ID__', deslocamentoId);
            }

            preencherDataHoraOculta('chegada');
            capturarLocalizacaoEEndereco('chegada');
            abrirModal('modalChegada');
        }

        async function abrirCameraEtapa(tipo) {
            tipoEtapaAtual = tipo;
            abrirModal('modalCameraEtapa');
            await iniciarCameraEtapa(cameraEtapaAtual);
        }

        async function iniciarCameraEtapa(facingMode = 'environment') {
            const status = document.getElementById('cameraEtapaStatus');
            const video = document.getElementById('cameraEtapaPreview');

            try {
                pararCameraEtapa(false);

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Seu navegador não suporta acesso à câmera.');
                    return;
                }

                if (status) status.textContent = 'Abrindo câmera...';

                cameraEtapaStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: facingMode } },
                    audio: false
                });

                cameraEtapaAtual = facingMode;

                if (video) {
                    video.srcObject = cameraEtapaStream;

                    if (cameraEtapaAtual === 'user') {
                        video.style.transform = 'scaleX(-1)';
                        if (status) status.textContent = 'Câmera frontal ativa.';
                    } else {
                        video.style.transform = 'scaleX(1)';
                        if (status) status.textContent = 'Câmera traseira ativa.';
                    }
                }
            } catch (error) {
                console.error(error);
                if (status) status.textContent = 'Não foi possível acessar a câmera.';
                alert('Não foi possível acessar a câmera. Verifique a permissão do navegador.');
            }
        }

        async function trocarCameraEtapa() {
            cameraEtapaAtual = cameraEtapaAtual === 'environment' ? 'user' : 'environment';
            await iniciarCameraEtapa(cameraEtapaAtual);
        }

        function pararCameraEtapa(fecharModalFlag = true) {
            const modal = document.getElementById('modalCameraEtapa');
            const video = document.getElementById('cameraEtapaPreview');
            const status = document.getElementById('cameraEtapaStatus');

            if (fecharModalFlag && modal) {
                modal.classList.remove('is-open');
            }

            if (video) video.srcObject = null;

            if (cameraEtapaStream) {
                cameraEtapaStream.getTracks().forEach(track => track.stop());
                cameraEtapaStream = null;
            }

            if (status) status.textContent = 'Aguardando câmera...';

            if (!document.querySelector('.custom-modal.is-open')) {
                document.body.classList.remove('modal-open');
            }
        }

        function fecharCameraEtapa() {
            pararCameraEtapa(true);
        }

        function capturarFotoEtapa() {
            const video = document.getElementById('cameraEtapaPreview');
            const canvas = document.getElementById('cameraEtapaCanvas');

            if (!video || !video.videoWidth || !video.videoHeight) {
                alert('A câmera ainda não está pronta.');
                return;
            }

            if (!tipoEtapaAtual) {
                alert('Tipo da etapa não identificado.');
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

            if (cameraEtapaAtual === 'user') {
                context.translate(canvas.width, 0);
                context.scale(-1, 1);
            }

            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const base64 = canvas.toDataURL('image/jpeg', 0.88);

            const campoBase64 = document.getElementById(`foto_${tipoEtapaAtual}_base64`);
            const campoNome = document.getElementById(`foto_${tipoEtapaAtual}_nome`);
            const campoMime = document.getElementById(`foto_${tipoEtapaAtual}_mime`);
            const previewBox = document.getElementById(`preview_${tipoEtapaAtual}_box`);
            const previewImg = document.getElementById(`preview_${tipoEtapaAtual}_img`);

            if (campoBase64) campoBase64.value = base64;
            if (campoNome) campoNome.value = `${tipoEtapaAtual}-` + Date.now() + '.jpg';
            if (campoMime) campoMime.value = 'image/jpeg';

            if (previewImg) previewImg.src = base64;
            if (previewBox) previewBox.classList.add('active');

            fecharCameraEtapa();
        }

        function limparFotoEtapa(tipo) {
            const campoBase64 = document.getElementById(`foto_${tipo}_base64`);
            const campoNome = document.getElementById(`foto_${tipo}_nome`);
            const campoMime = document.getElementById(`foto_${tipo}_mime`);
            const previewBox = document.getElementById(`preview_${tipo}_box`);
            const previewImg = document.getElementById(`preview_${tipo}_img`);

            if (campoBase64) campoBase64.value = '';
            if (campoNome) campoNome.value = '';
            if (campoMime) campoMime.value = 'image/jpeg';
            if (previewImg) previewImg.src = '';
            if (previewBox) previewBox.classList.remove('active');
        }

        async function geocodificarReverso(lat, lng) {
            try {
                const resp = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&addressdetails=1`,
                    { headers: { 'Accept': 'application/json' } }
                );

                if (!resp.ok) throw new Error('Erro');

                const data = await resp.json();
                const a = data.address || {};
                const partes = [];

                if (a.road) {
                    partes.push(a.house_number ? `${a.road}, ${a.house_number}` : a.road);
                }

                const bairro = a.suburb || a.neighbourhood || a.quarter;
                if (bairro) partes.push(bairro);

                const cidade = a.city || a.town || a.village || a.municipality;
                if (cidade) partes.push(cidade);

                if (a.postcode) partes.push(a.postcode);

                return partes.length ? partes.join(' – ') : `${lat}, ${lng}`;
            } catch (e) {
                return `${lat}, ${lng}`;
            }
        }

        document.addEventListener('DOMContentLoaded', async function () {
            const links = document.querySelectorAll('.gps-maps-link');

            for (const link of links) {
                const lat = link.dataset.lat;
                const lng = link.dataset.lng;
                const span = link.querySelector('.gps-endereco-texto');

                if (!span) continue;

                await new Promise(r => setTimeout(r, 350));

                const endereco = await geocodificarReverso(lat, lng);
                span.textContent = endereco;
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.getElementById('modalCameraEtapa')?.classList.contains('is-open')) {
                    fecharCameraEtapa();
                    return;
                }

                document.querySelectorAll('.custom-modal.is-open').forEach(modal => {
                    modal.classList.remove('is-open');
                });

                document.body.classList.remove('modal-open');
            }
        });

        document.getElementById('formSaida')?.addEventListener('submit', function(e) {
            preencherDataHoraOculta('saida');

            if (!document.getElementById('foto_saida_base64')?.value) {
                e.preventDefault();
                alert('Tire a foto do painel antes de registrar a saída.');
            }
        });

        document.getElementById('formParada')?.addEventListener('submit', function(e) {
            preencherDataHoraOculta('parada');

            if (!this.action) {
                e.preventDefault();
                alert('Não foi possível identificar o deslocamento da parada.');
                return;
            }

            if (!document.getElementById('foto_parada_base64')?.value) {
                e.preventDefault();
                alert('Tire a foto do painel antes de registrar a parada.');
            }
        });

        document.getElementById('formChegada')?.addEventListener('submit', function(e) {
            preencherDataHoraOculta('chegada');

            if (!this.action) {
                e.preventDefault();
                alert('Não foi possível identificar o deslocamento da chegada.');
                return;
            }

            if (!document.getElementById('foto_chegada_base64')?.value) {
                e.preventDefault();
                alert('Tire a foto do painel antes de registrar a chegada.');
            }
        });

        @if(session('open_modal_saida'))
            document.addEventListener('DOMContentLoaded', function () {
                abrirModalSaida();
            });
        @endif

        @if(session('open_modal_parada'))
            document.addEventListener('DOMContentLoaded', function () {
                abrirModalParada({{ session('open_modal_parada') }});
            });
        @endif

        @if(session('open_modal_chegada'))
            document.addEventListener('DOMContentLoaded', function () {
                abrirModalChegada({{ session('open_modal_chegada') }});
            });
        @endif

        window.addEventListener('beforeunload', function () {
            if (cameraEtapaStream) {
                cameraEtapaStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
@endsection