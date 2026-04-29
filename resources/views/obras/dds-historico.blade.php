@extends('layouts.app')

@section('title', 'Histórico de DDS')
@section('pageTitle', 'Histórico de DDS')
@section('pageDescription', 'Visualize e cadastre treinamentos DDS da obra.')

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

    .btn-danger-soft {
        background: var(--danger-soft);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.20);
    }

    .btn-danger-soft:hover {
        background: rgba(239, 68, 68, 0.18);
        border-color: rgba(239, 68, 68, 0.34);
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
    }
</style>

    <div class="page-head">
        <div class="page-head__left">
            <div class="page-head__icon"><i class="bi bi-journal-check"></i></div>
            <div class="page-head__text">
                <h2>Histórico de DDS - {{ $obra->nome }}</h2>
                <p>Cadastre treinamentos DDS e anexe fotos, imagens ou PDF.</p>
            </div>
        </div>

        <div class="actions-inline">
            <a href="{{ route('obras.index') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success-box">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
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
                <div class="card-title">Resumo da obra</div>
                <div class="card-subtitle">Dados principais e último DDS registrado.</div>
            </div>
        </div>

        <div class="card-body" style="padding:18px 20px;">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label"><i class="bi bi-building"></i> Obra</label>
                    <input type="text" class="form-control-custom" value="{{ $obra->nome }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-toggle-on"></i> Status</label>
                    <input type="text" class="form-control-custom" value="{{ ucfirst($obra->status) }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-calendar3"></i> Data de início</label>
                    <input type="text" class="form-control-custom" value="{{ $obra->data_inicio ? $obra->data_inicio->format('d/m/Y') : '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="bi bi-clock-history"></i> Último DDS</label>
                    <input type="text" class="form-control-custom" value="{{ $obra->ultimoTreinamentoDds?->data_treinamento ? $obra->ultimoTreinamentoDds->data_treinamento->format('d/m/Y') : 'Nenhum DDS' }}" readonly>
                </div>

                <div class="form-group form-group-full">
                    <label class="form-label"><i class="bi bi-geo-alt"></i> Endereço</label>
                    <input type="text" class="form-control-custom" value="{{ $obra->endereco ?: '-' }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <div>
                <div class="card-title">Novo DDS</div>
                <div class="card-subtitle">Cadastre uma nova data de DDS para esta obra.</div>
            </div>
        </div>

        <div class="card-body" style="padding:18px 20px;">
            <form
                action="{{ route('obras.dds.store', $obra) }}"
                method="POST"
                enctype="multipart/form-data"
                id="form-dds"
            >
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-calendar-check"></i> Data do DDS</label>
                        <input type="date" name="data_treinamento" class="form-control-custom" value="{{ old('data_treinamento', now()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="bi bi-paperclip"></i> Anexos</label>
                        <input type="file" name="anexos[]" id="arquivo-dds" class="form-control-custom" accept=".jpg,.jpeg,.png,.pdf,application/pdf,image/*" multiple>
                    </div>

                    <div class="form-group form-group-full">
                        <label class="form-label"><i class="bi bi-chat-left-text"></i> Observações</label>
                        <textarea name="observacoes" class="form-control-custom" rows="4">{{ old('observacoes') }}</textarea>
                    </div>

                    <input type="hidden" name="foto_camera_base64" id="foto-camera-base64">
                    <input type="hidden" name="foto_camera_nome" id="foto-camera-nome">
                    <input type="hidden" name="foto_camera_mime" id="foto-camera-mime">

                    <div class="form-group-full actions-inline">
                        <button type="button" class="btn btn-dark" onclick="abrirOpcoesComprovante()">
                            <i class="bi bi-camera"></i>
                            Tirar foto / anexar
                        </button>

                        <button type="submit" class="btn btn-green">
                            <i class="bi bi-check2-circle"></i>
                            Salvar DDS
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">DDS registrados ({{ $obra->treinamentosDds->count() }})</div>
                <div class="card-subtitle">Lista completa dos treinamentos registrados para a obra.</div>
            </div>
        </div>

        <div class="card-body" style="padding:18px 20px;">
                        @forelse($obra->treinamentosDds as $treinamento)
                <div class="card section-card">
                    <div class="card-header">
                        <div>
                            <div class="card-title" style="font-size:16px;">
                                DDS em {{ $treinamento->data_treinamento?->format('d/m/Y') }}
                            </div>
                            <div class="card-subtitle">
                                Registrado por: {{ $treinamento->usuario->name ?? 'Sistema' }}
                            </div>
                        </div>

                        <div class="actions-inline">
                            <form method="POST" action="{{ route('obras.dds.destroy', $treinamento) }}" onsubmit="return confirm('Tem certeza que deseja excluir este DDS?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-soft">
                                    <i class="bi bi-trash"></i>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body" style="padding:16px;">
                        @if($treinamento->observacoes)
                            <div class="alert-success-box" style="margin-bottom:14px;">
                                <i class="bi bi-chat-left-text"></i>
                                {{ $treinamento->observacoes }}
                            </div>
                        @endif

                        <div>
                            <strong>Anexos</strong>

                            <div class="simple-list" style="margin-top:10px;">
                                @forelse($treinamento->anexos as $anexo)
                                    <div class="simple-item">
                                        <div>
                                            <strong>{{ $anexo->nome_original ?: 'Arquivo anexado' }}</strong>
                                        </div>

                                        <div class="text-muted-small">
                                            Enviado em {{ $anexo->created_at?->format('d/m/Y H:i') }}
                                        </div>

                                        <div class="text-muted-small">
                                            Tipo: {{ $anexo->mime_type ?: '-' }}
                                        </div>

                                        <div style="margin-top:8px;">
                                            <a href="{{ route('obras.dds.anexo.abrir', $anexo) }}" target="_blank" class="btn btn-dark">
                                                <i class="bi bi-eye"></i>
                                                {{ $anexo->mime_type === 'application/pdf' ? 'Abrir PDF' : 'Abrir arquivo' }}
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted-small">Nenhum anexo enviado.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert-error-box">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Nenhum DDS registrado para esta obra.
                </div>
            @endforelse
        </div>
    </div>

    <div id="modal-comprovante" style="display:none;">
        <div class="modal-comprovante-overlay" onclick="fecharOpcoesComprovante()"></div>

        <div class="modal-comprovante-box">
            <div class="modal-comprovante-title">Como deseja anexar?</div>

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
            <div class="modal-comprovante-title">Tirar foto do DDS</div>

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
            <div class="modal-comprovante-title">Ajustar imagem</div>

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
        let cameraStream = null;
        let cameraAtual = 'environment';
        let cropper = null;

        function abrirOpcoesComprovante() {
            document.getElementById('modal-comprovante').style.display = 'block';
        }

        function fecharOpcoesComprovante() {
            document.getElementById('modal-comprovante').style.display = 'none';
        }

        async function abrirCamera() {
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
                    video: { facingMode: { ideal: facingMode } },
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
                alert('Não foi possível acessar a câmera.');
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
            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('camera-canvas');

            if (!video.videoWidth || !video.videoHeight) {
                alert('A câmera ainda não está pronta.');
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

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
            document.getElementById('foto-camera-base64').value = base64;
            document.getElementById('foto-camera-nome').value = nomeArquivo;
            document.getElementById('foto-camera-mime').value = mimeType;

            fecharCropper();
        }

        function enviarComoImagem() {
            if (!cropper) {
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
            const nome = 'dds-' + Date.now() + '.jpg';

            prepararSubmit(base64, nome, 'image/jpeg');
        }

        async function enviarComoPdf() {
            if (!cropper) {
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
                const nome = 'dds-' + Date.now() + '.pdf';

                prepararSubmit(base64, nome, 'application/pdf');
            };
            reader.readAsDataURL(pdfBlob);
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
                    fecharOpcoesComprovante();
                    document.getElementById('arquivo-dds').click();
                });
            }

            if (btnCamera) {
                btnCamera.addEventListener('click', abrirCamera);
            }

            if (btnCapturar) {
                btnCapturar.addEventListener('click', capturarFoto);
            }

            if (btnTrocarCamera) {
                btnTrocarCamera.addEventListener('click', trocarCamera);
            }

            if (btnEnviarImagem) {
                btnEnviarImagem.addEventListener('click', enviarComoImagem);
            }

            if (btnEnviarPdf) {
                btnEnviarPdf.addEventListener('click', enviarComoPdf);
            }
        });

        window.addEventListener('beforeunload', function () {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
@endsection