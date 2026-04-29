@extends('layouts.app')

@section('title', 'Histórico de EPI')
@section('pageTitle', 'Histórico de EPI')
@section('pageDescription', 'Visualize todas as entregas e comprovantes do funcionário.')

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

    .card-body {
        padding: 0;
        background: transparent;
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

    .simple-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
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

    .section-card {
        margin-bottom: 16px;
        border: 1px solid rgba(255,255,255,.08);
    }

    .modal-comprovante-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 9998;
    }

    .modal-comprovante-box {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: min(92%, 360px);
        background: #1f1f1f;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 16px;
        padding: 18px;
        z-index: 9999;
        box-shadow: 0 20px 50px rgba(0,0,0,.35);
    }

    .modal-comprovante-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 14px;
        text-align: center;
        color: #fff;
    }

    .modal-comprovante-actions {
        display: grid;
        gap: 10px;
    }

    #crop-image {
        max-height: 65vh;
        width: 100%;
        object-fit: contain;
    }

    .cropper-container {
        max-width: 100%;
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

        .card-body {
            padding: 0;
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

        .form-control-custom {
            width: 100%;
        }
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-clock-history"></i></div>
            <div class="page-head__text">
                <h2>Histórico de EPI</h2>
                <p>Veja todas as entregas, itens e comprovantes anexados do funcionário.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('entregas.index') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>

            <a href="{{ route('entregas.index', ['open_create' => 1, 'obra_id' => $funcionario->obra_id, 'funcionario_id' => $funcionario->id]) }}" class="btn btn-green">
                <i class="bi bi-plus-circle"></i>
                Nova entrega
            </a>

            <a href="{{ route('epi.pdf.ultima', $funcionario->id) }}" class="btn btn-dark">
                <i class="bi bi-file-earmark-pdf"></i>
                Baixar última entrega
            </a>

            <a href="{{ route('epi.pdf.completo', $funcionario->id) }}" class="btn btn-dark">
                <i class="bi bi-file-earmark-pdf"></i>
                Baixar histórico completo
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success-box">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <div>
                <div class="card-title">{{ $funcionario->nome }}</div>
                <div class="card-subtitle">Resumo do funcionário</div>
            </div>
        </div>

        <div class="card-body" style="padding:18px 20px;">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label"><i class="bi bi-person"></i> Nome</label>
                    <input type="text" class="form-control-custom" value="{{ $funcionario->nome }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-hash"></i> Matrícula</label>
                    <input type="text" class="form-control-custom" value="{{ $funcionario->matricula ?: '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-person-vcard"></i> CPF</label>
                    <input type="text" class="form-control-custom" value="{{ $funcionario->cpf ?: '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-telephone"></i> Telefone</label>
                    <input type="text" class="form-control-custom" value="{{ $funcionario->telefone ?: '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-building"></i> Obra</label>
                    <input type="text" class="form-control-custom" value="{{ $funcionario->obra->nome ?? '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-briefcase"></i> Cargo</label>
                    <input type="text" class="form-control-custom" value="{{ $funcionario->cargo->nome ?? '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-calendar3"></i> Data de admissão</label>
                    <input
                        type="text"
                        class="form-control-custom"
                        value="{{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : '-' }}"
                        readonly
                    >
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-box-seam"></i> Última entrega</label>
                    <input
                        type="text"
                        class="form-control-custom"
                        value="{{ $ultimaEntrega?->data_entrega ? $ultimaEntrega->data_entrega->format('d/m/Y') : 'Nenhuma entrega' }}"
                        readonly
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Entregas registradas ({{ $funcionario->entregasEpi->count() }})</div>
                <div class="card-subtitle">Cada bloco representa um lote de entrega.</div>
            </div>
        </div>

        <div class="card-body" style="padding:18px 20px;">            @forelse($funcionario->entregasEpi as $entrega)
                <div class="card section-card">
                    <div class="card-header">
                        <div>
                            <div class="card-title" style="font-size:16px;">
                                Entrega #{{ $entrega->id }} - {{ $entrega->data_entrega?->format('d/m/Y') }}
                            </div>
                            <div class="card-subtitle">
                                Registrado por: {{ $entrega->usuario->name ?? 'Sistema' }}
                            </div>
                        </div>

                        <div>
                            @if($entrega->status_comprovante === 'anexado')
                                <span class="badge-status badge-success">Comprovante anexado</span>
                            @else
                                <span class="badge-status badge-warning">Comprovante pendente</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body" style="padding:16px;">
                        @if($entrega->observacoes)
                            <div class="alert-success-box" style="margin-bottom:14px;">
                                <i class="bi bi-chat-left-text"></i>
                                {{ $entrega->observacoes }}
                            </div>
                        @endif

                        <div class="table-wrap" style="margin-bottom:16px;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Variação</th>
                                        <th>Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entrega->itens as $item)
                                        <tr>
                                            <td data-label="Produto">{{ $item->produto->nome ?? '-' }}</td>
                                            <td data-label="Variação">
                                                @if($item->variacao)
                                                    <strong>{{ $item->variacao->nome_variacao }}</strong>
                                                    <div class="text-muted-small">
                                                        {{ $item->variacao->cor ?? '' }}
                                                        {{ $item->variacao->tamanho ?? '' }}
                                                        @if($item->variacao->sku)
                                                            | SKU: {{ $item->variacao->sku }}
                                                        @endif
                                                    </div>
                                                @else
                                                    Sem variação
                                                @endif
                                            </td>
                                            <td data-label="Quantidade">
                                                {{ number_format((float) $item->quantidade, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <div class="text-muted-small">Nenhum item registrado.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:16px; margin-bottom:16px;">
                            <form
                                action="{{ route('entregas.comprovantes.upload', $entrega->id) }}"
                                method="POST"
                                enctype="multipart/form-data"
                                class="upload-auto-form"
                                id="form-upload-{{ $entrega->id }}"
                            >
                                @csrf

                                <input
                                    type="file"
                                    name="comprovantes[]"
                                    id="arquivo-{{ $entrega->id }}"
                                    accept=".jpg,.jpeg,.png,.pdf,application/pdf,image/*"
                                    multiple
                                    style="display:none;"
                                    onchange="handleAutoUpload(this)"
                                >

                                <input type="hidden" name="foto_camera_base64" id="foto-camera-base64-{{ $entrega->id }}">
                                <input type="hidden" name="foto_camera_nome" id="foto-camera-nome-{{ $entrega->id }}">
                                <input type="hidden" name="foto_camera_mime" id="foto-camera-mime-{{ $entrega->id }}">

                                <div class="actions-inline">
                                    <button
                                        type="button"
                                        class="btn btn-green"
                                        id="btn-upload-{{ $entrega->id }}"
                                        onclick="abrirOpcoesComprovante({{ $entrega->id }})"
                                    >
                                        <i class="bi bi-upload"></i>
                                        Anexar comprovante(s)
                                    </button>
                                </div>

                                <div class="text-muted-small" style="margin-top:8px;">
                                    Você pode enviar arquivo, tirar foto, recortar e converter em PDF.
                                </div>
                            </form>
                        </div>

                        <div>
                            <strong>Comprovantes anexados</strong>

                            <div class="simple-list" style="margin-top:10px;">
                                @forelse($entrega->comprovantes as $comprovante)
                                    <div class="simple-item">
                                        <div>
                                            <strong>{{ $comprovante->nome_original ?: 'Arquivo anexado' }}</strong>
                                        </div>

                                        <div class="text-muted-small">
                                            Enviado em {{ $comprovante->created_at?->format('d/m/Y H:i') }}
                                        </div>

                                        <div class="text-muted-small">
                                            Tipo: {{ $comprovante->mime_type ?: '-' }}
                                        </div>

                                        <div style="margin-top:8px;">
                                            <a href="{{ route('entregas.comprovantes.abrir', $comprovante->id) }}" target="_blank" class="btn btn-dark">
                                                <i class="bi bi-eye"></i>
                                                {{ $comprovante->mime_type === 'application/pdf' ? 'Abrir PDF' : 'Abrir arquivo' }}
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted-small">Nenhum comprovante anexado.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert-error-box">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Nenhuma entrega encontrada para este funcionário.
                </div>
            @endforelse
        </div>
    </div>

    <div id="modal-comprovante" style="display:none;">
        <div class="modal-comprovante-overlay" onclick="fecharOpcoesComprovante()"></div>

        <div class="modal-comprovante-box">
            <div class="modal-comprovante-title">Como deseja anexar o comprovante?</div>

            <div class="modal-comprovante-actions">
                <button type="button" class="btn btn-green" id="btn-escolher-arquivo">
                    <i class="bi bi-folder2-open"></i>
                    Enviar arquivo
                </button>

                <button type="button" class="btn btn-dark" id="btn-tirar-foto">
                    <i class="bi bi-camera"></i>
                    Tirar foto
                </button>
            </div>

            <div style="margin-top:10px; text-align:center;">
                <button type="button" class="btn btn-dark" onclick="fecharOpcoesComprovante()">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <div id="modal-camera" style="display:none;">
        <div class="modal-comprovante-overlay"></div>

        <div class="modal-comprovante-box" style="width:min(95%, 460px);">
            <div class="modal-comprovante-title">Tirar foto do comprovante</div>

            <video
                id="camera-preview"
                autoplay
                playsinline
                muted
                style="width:100%; border-radius:12px; background:#000; max-height:70vh; object-fit:cover; transform:scaleX(-1);"
            ></video>

            <canvas id="camera-canvas" style="display:none;"></canvas>

            <div class="modal-comprovante-actions" style="margin-top:12px;">
                <button type="button" class="btn btn-dark" id="btn-trocar-camera">
                    <i class="bi bi-arrow-repeat"></i>
                    Trocar câmera
                </button>

                <button type="button" class="btn btn-green" id="btn-capturar-foto">
                    <i class="bi bi-camera"></i>
                    Capturar foto
                </button>

                <button type="button" class="btn btn-dark" onclick="fecharCamera()">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <div id="modal-crop" style="display:none;">
        <div class="modal-comprovante-overlay"></div>

        <div class="modal-comprovante-box" style="width:min(96%, 520px);">
            <div class="modal-comprovante-title">Ajustar comprovante</div>

            <div style="margin-bottom:12px;">
                <img id="crop-image" style="max-width:100%; display:block; border-radius:12px;">
            </div>

            <div class="modal-comprovante-actions">
                <button type="button" class="btn btn-green" id="btn-enviar-imagem">
                    <i class="bi bi-image"></i>
                    Enviar como imagem
                </button>

                <button type="button" class="btn btn-dark" id="btn-enviar-pdf">
                    <i class="bi bi-file-earmark-pdf"></i>
                    Enviar como PDF
                </button>

                <button type="button" class="btn btn-dark" onclick="fecharCropper()">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
    <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let entregaAtualUpload = null;
        let cameraStream = null;
        let cameraAtual = 'environment';
        let cropper = null;

        function abrirOpcoesComprovante(entregaId) {
            entregaAtualUpload = entregaId;
            document.getElementById('modal-comprovante').style.display = 'block';
        }

        function fecharOpcoesComprovante() {
            document.getElementById('modal-comprovante').style.display = 'none';
        }

        async function abrirCamera() {
            if (!entregaAtualUpload) {
                alert('Nenhuma entrega selecionada.');
                return;
            }

            fecharOpcoesComprovante();
            await iniciarCamera(cameraAtual);
        }

        async function iniciarCamera(facingMode = 'environment') {
            try {
                fecharCamera(false);

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Seu navegador não suporta acesso à câmera.');
                    return;
                }

                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: facingMode }
                    },
                    audio: false
                });

                cameraAtual = facingMode;

                const video = document.getElementById('camera-preview');
                video.srcObject = cameraStream;

                if (cameraAtual === 'user') {
                    video.style.transform = 'scaleX(1)';
                } else {
                    video.style.transform = 'scaleX(-1)';
                }

                document.getElementById('modal-camera').style.display = 'block';
            } catch (error) {
                console.error(error);
                alert('Não foi possível acessar a câmera. Verifique a permissão do navegador e se o site está em HTTPS.');
            }
        }

        async function trocarCamera() {
            cameraAtual = cameraAtual === 'environment' ? 'user' : 'environment';
            await iniciarCamera(cameraAtual);
        }

        function fecharCamera(fecharModal = true) {
            if (fecharModal) {
                document.getElementById('modal-camera').style.display = 'none';
            }

            const video = document.getElementById('camera-preview');
            if (video) {
                video.srcObject = null;
            }

            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        function capturarFoto() {
            if (!entregaAtualUpload) {
                alert('Nenhuma entrega selecionada.');
                return;
            }

            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('camera-canvas');

            if (!video.videoWidth || !video.videoHeight) {
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

            context.save();

            if (cameraAtual === 'environment') {
                context.translate(canvas.width, 0);
                context.scale(-1, 1);
            }

            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            context.restore();

            const imagemBase64 = canvas.toDataURL('image/jpeg', 0.88);

            fecharCamera(true);
            abrirCropper(imagemBase64);
        }

        function abrirCropper(base64) {
            const modal = document.getElementById('modal-crop');
            const image = document.getElementById('crop-image');

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            image.onload = function () {
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(image, {
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                    zoomable: true,
                    scalable: false,
                    rotatable: false,
                    movable: true,
                });
            };

            image.src = base64;
            modal.style.display = 'block';
        }

        function fecharCropper() {
            document.getElementById('modal-crop').style.display = 'none';

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }

        function prepararSubmit(base64, nomeArquivo, mimeType) {
            const inputBase64 = document.getElementById('foto-camera-base64-' + entregaAtualUpload);
            const inputNome = document.getElementById('foto-camera-nome-' + entregaAtualUpload);
            const inputMime = document.getElementById('foto-camera-mime-' + entregaAtualUpload);
            const form = document.getElementById('form-upload-' + entregaAtualUpload);
            const button = document.getElementById('btn-upload-' + entregaAtualUpload);

            if (!inputBase64 || !inputNome || !inputMime || !form) {
                alert('Não foi possível preparar o envio.');
                return;
            }

            inputBase64.value = base64;
            inputNome.value = nomeArquivo;
            inputMime.value = mimeType;

            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
            }

            fecharCropper();
            form.submit();
        }

        function enviarComoImagem() {
            if (!cropper || !entregaAtualUpload) {
                alert('Imagem não disponível para envio.');
                return;
            }

            const canvas = cropper.getCroppedCanvas({
                maxWidth: 1600,
                maxHeight: 1600,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: '#ffffff'
            });

            const base64 = canvas.toDataURL('image/jpeg', 0.88);
            const nome = 'comprovante-' + Date.now() + '.jpg';

            prepararSubmit(base64, nome, 'image/jpeg');
        }

        async function enviarComoPdf() {
            if (!cropper || !entregaAtualUpload) {
                alert('Imagem não disponível para envio.');
                return;
            }

            const canvas = cropper.getCroppedCanvas({
                maxWidth: 1800,
                maxHeight: 1800,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: '#ffffff'
            });

            const imageBase64 = canvas.toDataURL('image/jpeg', 0.90);
            const { jsPDF } = window.jspdf;

            const pdf = new jsPDF({
                orientation: canvas.width > canvas.height ? 'landscape' : 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const imgProps = pdf.getImageProperties(imageBase64);
            const ratio = Math.min(pageWidth / imgProps.width, pageHeight / imgProps.height);

            const imgWidth = imgProps.width * ratio;
            const imgHeight = imgProps.height * ratio;
            const x = (pageWidth - imgWidth) / 2;
            const y = (pageHeight - imgHeight) / 2;

            pdf.addImage(imageBase64, 'JPEG', x, y, imgWidth, imgHeight);

            const pdfBlob = pdf.output('blob');

            const reader = new FileReader();
            reader.onloadend = function () {
                const base64 = reader.result;
                const nome = 'comprovante-' + Date.now() + '.pdf';

                prepararSubmit(base64, nome, 'application/pdf');
            };
            reader.readAsDataURL(pdfBlob);
        }

        function handleAutoUpload(input) {
            if (!input.files || input.files.length === 0) {
                return;
            }

            const form = input.form;
            const button = form.querySelector('button[id^="btn-upload-"]');

            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
            }

            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const btnArquivo = document.getElementById('btn-escolher-arquivo');
            const btnCamera = document.getElementById('btn-tirar-foto');
            const btnCapturar = document.getElementById('btn-capturar-foto');
            const btnTrocarCamera = document.getElementById('btn-trocar-camera');
            const btnEnviarImagem = document.getElementById('btn-enviar-imagem');
            const btnEnviarPdf = document.getElementById('btn-enviar-pdf');

            if (btnArquivo) {
                btnArquivo.addEventListener('click', function () {
                    if (!entregaAtualUpload) {
                        alert('Nenhuma entrega selecionada.');
                        return;
                    }

                    const inputArquivo = document.getElementById('arquivo-' + entregaAtualUpload);

                    fecharOpcoesComprovante();

                    if (inputArquivo) {
                        inputArquivo.click();
                    }
                });
            }

            if (btnCamera) {
                btnCamera.addEventListener('click', function () {
                    abrirCamera();
                });
            }

            if (btnCapturar) {
                btnCapturar.addEventListener('click', function () {
                    capturarFoto();
                });
            }

            if (btnTrocarCamera) {
                btnTrocarCamera.addEventListener('click', function () {
                    trocarCamera();
                });
            }

            if (btnEnviarImagem) {
                btnEnviarImagem.addEventListener('click', function () {
                    enviarComoImagem();
                });
            }

            if (btnEnviarPdf) {
                btnEnviarPdf.addEventListener('click', function () {
                    enviarComoPdf();
                });
            }
        });

        window.addEventListener('beforeunload', function () {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
@endsection