<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $vaga->titulo }} — JPC Construções & Incorporações</title>
    <meta name="description" content="Candidate-se à vaga de {{ $vaga->titulo }} na JPC Construções & Incorporações. {{ Str::limit($vaga->descricao, 140) }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Inter',sans-serif;min-height:100vh;background:#050a14;color:#e2e8f0;overflow-x:hidden}

    /* ── Background decoration ── */
    .ap-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
    .ap-bg::before{content:"";position:absolute;width:800px;height:800px;top:-300px;left:50%;transform:translateX(-50%);background:radial-gradient(circle,rgba(59,130,246,.08),transparent 65%);border-radius:50%}
    .ap-bg::after{content:"";position:absolute;width:600px;height:600px;bottom:-200px;right:-100px;background:radial-gradient(circle,rgba(139,92,246,.05),transparent 65%);border-radius:50%}
    .ap-grid-bg{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(148,163,184,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,.03) 1px,transparent 1px);background-size:60px 60px}

    /* ── Top bar ── */
    .ap-topbar{position:relative;z-index:2;padding:16px 24px;display:flex;align-items:center;justify-content:center;border-bottom:1px solid rgba(148,163,184,.06);background:rgba(5,10,20,.6);backdrop-filter:blur(12px)}
    .ap-logo{height:32px;opacity:.85;transition:opacity .2s}
    .ap-logo:hover{opacity:1}

    /* ── Layout ── */
    .ap-wrapper{position:relative;z-index:1;max-width:1000px;margin:0 auto;padding:32px 20px 60px;display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:start}

    /* ── Left column: Job details ── */
    .ap-details{position:sticky;top:24px}

    .ap-badge-row{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}
    .ap-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.03em}
    .ap-badge-open{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.18);color:#86efac}
    .ap-badge-type{background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.18);color:#93c5fd}

    .ap-title{font-size:26px;font-weight:800;color:#f8fafc;letter-spacing:-.03em;line-height:1.2;margin-bottom:6px}
    .ap-company{font-size:13.5px;color:#64748b;font-weight:500;margin-bottom:20px}

    .ap-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:20px}
    .ap-meta-item{display:flex;align-items:center;gap:8px;padding:12px 14px;border-radius:12px;background:rgba(255,255,255,.02);border:1px solid rgba(148,163,184,.08)}
    .ap-meta-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
    .ap-meta-icon.geo{background:rgba(59,130,246,.1);color:#3b82f6}
    .ap-meta-icon.cash{background:rgba(34,197,94,.1);color:#22c55e}
    .ap-meta-icon.contract{background:rgba(168,85,247,.1);color:#a855f7}
    .ap-meta-icon.date{background:rgba(245,158,11,.1);color:#f59e0b}
    .ap-meta-text span{display:block;font-size:10.5px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em}
    .ap-meta-text strong{display:block;font-size:13px;color:#e2e8f0;font-weight:600;margin-top:1px}

    .ap-section{margin-bottom:16px}
    .ap-section-header{display:flex;align-items:center;gap:8px;margin-bottom:10px}
    .ap-section-icon{width:28px;height:28px;border-radius:8px;background:rgba(59,130,246,.08);display:flex;align-items:center;justify-content:center;font-size:13px;color:#3b82f6}
    .ap-section-label{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8}
    .ap-section-body{padding:16px;border-radius:14px;background:rgba(255,255,255,.02);border:1px solid rgba(148,163,184,.07);font-size:13.5px;color:#94a3b8;line-height:1.7;white-space:pre-line}

    /* ── Right column: Form ── */
    .ap-form-wrap{background:rgba(15,23,42,.5);border:1px solid rgba(148,163,184,.1);border-radius:22px;padding:28px;backdrop-filter:blur(12px)}
    .ap-form-header{text-align:center;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid rgba(148,163,184,.08)}
    .ap-form-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,rgba(59,130,246,.15),rgba(139,92,246,.1));border:1px solid rgba(59,130,246,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:20px;color:#93c5fd}
    .ap-form-title{font-size:18px;font-weight:700;color:#f8fafc;margin-bottom:4px}
    .ap-form-subtitle{font-size:12.5px;color:#64748b}

    .ap-group{margin-bottom:14px}
    .ap-label{display:block;margin-bottom:5px;font-size:12px;font-weight:600;color:#94a3b8}
    .ap-label .req{color:#ef4444;margin-left:2px}
    .ap-input{width:100%;min-height:44px;padding:11px 14px;border-radius:11px;border:1px solid rgba(148,163,184,.14);background:rgba(11,18,32,.6);color:#f8fafc;font-size:13.5px;font-family:inherit;outline:none;transition:all .2s}
    .ap-input:focus{border-color:rgba(59,130,246,.4);box-shadow:0 0 0 3px rgba(59,130,246,.08);background:rgba(11,18,32,.8)}
    .ap-input::placeholder{color:#475569}
    textarea.ap-input{min-height:72px;resize:vertical}
    select.ap-input{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");background-repeat:no-repeat;background-position:right 14px center;padding-right:38px}

    .ap-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}

    .ap-file-area{border:2px dashed rgba(148,163,184,.14);border-radius:14px;padding:24px 16px;text-align:center;cursor:pointer;transition:all .25s;position:relative;background:rgba(11,18,32,.4)}
    .ap-file-area:hover,.ap-file-area.dragover{border-color:rgba(59,130,246,.35);background:rgba(59,130,246,.03)}
    .ap-file-icon{width:44px;height:44px;border-radius:12px;background:rgba(59,130,246,.08);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:20px;color:#3b82f6;transition:transform .2s}
    .ap-file-area:hover .ap-file-icon{transform:translateY(-2px)}
    .ap-file-area p{font-size:13px;color:#94a3b8}
    .ap-file-area .ap-file-hint{font-size:11px;color:#475569;margin-top:4px}
    .ap-file-area .ap-file-name{font-size:13px;color:#86efac;font-weight:600;margin-top:10px;display:none;padding:8px 12px;background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.15);border-radius:8px}
    .ap-file-area input{position:absolute;inset:0;opacity:0;cursor:pointer}

    .ap-divider{height:1px;background:rgba(148,163,184,.08);margin:18px 0}
    .ap-questions-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:14px;display:flex;align-items:center;gap:6px}
    .ap-questions-title i{color:#3b82f6}

    .ap-submit{width:100%;min-height:48px;border-radius:12px;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;font-size:14.5px;font-weight:700;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 6px 20px rgba(37,99,235,.2);margin-top:20px}
    .ap-submit:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(37,99,235,.3)}
    .ap-submit:active{transform:translateY(0)}

    .ap-error{font-size:11.5px;color:#fca5a5;margin-top:3px}

    /* ── Alerts ── */
    .ap-alert{padding:16px 18px;border-radius:14px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;font-size:14px;font-weight:500;line-height:1.5}
    .ap-alert i{font-size:18px;flex-shrink:0;margin-top:1px}
    .ap-alert-success{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.16);color:#86efac}
    .ap-alert-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.16);color:#fca5a5}

    .ap-success-card{text-align:center;padding:48px 28px;background:rgba(15,23,42,.5);border:1px solid rgba(148,163,184,.1);border-radius:22px;backdrop-filter:blur(12px)}
    .ap-success-icon{width:64px;height:64px;border-radius:50%;background:rgba(34,197,94,.1);border:2px solid rgba(34,197,94,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#22c55e}
    .ap-success-title{font-size:20px;font-weight:700;color:#f8fafc;margin-bottom:6px}
    .ap-success-text{font-size:14px;color:#94a3b8;line-height:1.6;max-width:360px;margin:0 auto}

    /* ── Footer ── */
    .ap-footer{position:relative;z-index:1;text-align:center;padding:24px 20px 32px;border-top:1px solid rgba(148,163,184,.05)}
    .ap-footer p{font-size:11.5px;color:#475569}
    .ap-footer a{color:#64748b;text-decoration:none}

    /* ── Responsive ── */
    @media(max-width:768px){
        .ap-wrapper{grid-template-columns:1fr;padding:20px 14px 40px}
        .ap-details{position:static}
        .ap-title{font-size:22px}
        .ap-meta-grid{grid-template-columns:1fr}
        .ap-form-wrap{padding:20px}
        .ap-row{grid-template-columns:1fr}
    }
    </style>
</head>
<body>
    <div class="ap-bg"></div>
    <div class="ap-grid-bg"></div>

    <!-- Top bar -->
    <div class="ap-topbar">
        <img src="/assets/imgs/logo.png" alt="JPC Construções & Incorporações" class="ap-logo">
    </div>

    @if(session('success'))
        <div class="ap-wrapper" style="display:block;max-width:560px;padding-top:60px">
            <div class="ap-success-card">
                <div class="ap-success-icon"><i class="bi bi-check-lg"></i></div>
                <div class="ap-success-title">Candidatura enviada!</div>
                <p class="ap-success-text">Recebemos sua candidatura para a vaga de <strong>{{ $vaga->titulo }}</strong>. Entraremos em contato em breve pelo telefone ou WhatsApp informado.</p>
            </div>
        </div>
    @else
        @if(session('error'))
            <div style="max-width:1000px;margin:20px auto 0;padding:0 20px">
                <div class="ap-alert ap-alert-error"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
            </div>
        @endif

        <div class="ap-wrapper">
            <!-- Left: Job Details -->
            <div class="ap-details">
                <div class="ap-badge-row">
                    <span class="ap-badge ap-badge-open"><i class="bi bi-circle-fill" style="font-size:6px"></i> Vaga Aberta</span>
                    @if($vaga->tipo_contrato)
                        <span class="ap-badge ap-badge-type"><i class="bi bi-tag-fill" style="font-size:9px"></i> {{ $vaga->tipo_contrato }}</span>
                    @endif
                </div>

                <h1 class="ap-title">{{ $vaga->titulo }}</h1>
                <p class="ap-company"><i class="bi bi-building"></i> JPC Construções & Incorporações</p>

                @if($vaga->local || $vaga->salario || $vaga->tipo_contrato || $vaga->data_limite)
                <div class="ap-meta-grid">
                    @if($vaga->local)
                    <div class="ap-meta-item">
                        <div class="ap-meta-icon geo"><i class="bi bi-geo-alt-fill"></i></div>
                        <div class="ap-meta-text"><span>Localização</span><strong>{{ $vaga->local }}</strong></div>
                    </div>
                    @endif
                    @if($vaga->salario)
                    <div class="ap-meta-item">
                        <div class="ap-meta-icon cash"><i class="bi bi-cash-stack"></i></div>
                        <div class="ap-meta-text"><span>Remuneração</span><strong>{{ $vaga->salario }}</strong></div>
                    </div>
                    @endif
                    @if($vaga->tipo_contrato)
                    <div class="ap-meta-item">
                        <div class="ap-meta-icon contract"><i class="bi bi-file-earmark-text-fill"></i></div>
                        <div class="ap-meta-text"><span>Contrato</span><strong>{{ $vaga->tipo_contrato }}</strong></div>
                    </div>
                    @endif
                    @if($vaga->data_limite)
                    <div class="ap-meta-item">
                        <div class="ap-meta-icon date"><i class="bi bi-calendar-event-fill"></i></div>
                        <div class="ap-meta-text"><span>Inscrições até</span><strong>{{ $vaga->data_limite->format('d/m/Y') }}</strong></div>
                    </div>
                    @endif
                </div>
                @endif

                @if($vaga->descricao)
                <div class="ap-section">
                    <div class="ap-section-header">
                        <div class="ap-section-icon"><i class="bi bi-info-circle-fill"></i></div>
                        <div class="ap-section-label">Sobre a vaga</div>
                    </div>
                    <div class="ap-section-body">{{ $vaga->descricao }}</div>
                </div>
                @endif

                @if($vaga->requisitos)
                <div class="ap-section">
                    <div class="ap-section-header">
                        <div class="ap-section-icon"><i class="bi bi-check2-square"></i></div>
                        <div class="ap-section-label">Requisitos</div>
                    </div>
                    <div class="ap-section-body">{{ $vaga->requisitos }}</div>
                </div>
                @endif

                @if($vaga->diferenciais)
                <div class="ap-section">
                    <div class="ap-section-header">
                        <div class="ap-section-icon"><i class="bi bi-stars"></i></div>
                        <div class="ap-section-label">Diferenciais</div>
                    </div>
                    <div class="ap-section-body">{{ $vaga->diferenciais }}</div>
                </div>
                @endif


                @if($vaga->beneficios)
                <div class="ap-section">
                    <div class="ap-section-header">
                        <div class="ap-section-icon"><i class="bi bi-gift-fill"></i></div>
                        <div class="ap-section-label">Benefícios</div>
                    </div>
                    <div class="ap-section-body">{{ $vaga->beneficios }}</div>
                </div>
                @endif
            </div>

            <!-- Right: Application Form -->
            <div class="ap-form-wrap">
                <div class="ap-form-header">
                    <div class="ap-form-icon"><i class="bi bi-send-fill"></i></div>
                    <div class="ap-form-title">Candidate-se agora</div>
                    <p class="ap-form-subtitle">Preencha seus dados e envie seu currículo</p>
                </div>

                <form method="POST" action="{{ route('vagas.aplicar.submit', $vaga->slug) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="ap-group">
                        <label class="ap-label">Nome completo <span class="req">*</span></label>
                        <input type="text" name="nome" class="ap-input" placeholder="Seu nome completo" value="{{ old('nome') }}" required>
                        @error('nome')<div class="ap-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="ap-row">
                        <div class="ap-group">
                            <label class="ap-label">Telefone (WhatsApp) <span class="req">*</span></label>
                            <input type="tel" name="telefone" class="ap-input" placeholder="(00) 00000-0000" value="{{ old('telefone') }}" required>
                            @error('telefone')<div class="ap-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="ap-group">
                            <label class="ap-label">E-mail</label>
                            <input type="email" name="email" class="ap-input" placeholder="seu@email.com" value="{{ old('email') }}">
                            @error('email')<div class="ap-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="ap-group">
                        <label class="ap-label">Currículo <span class="req">*</span></label>
                        <div class="ap-file-area" id="fileArea">
                            <div class="ap-file-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                            <p>Clique ou arraste seu currículo</p>
                            <p class="ap-file-hint">PDF, DOC ou DOCX — máx. 10 MB</p>
                            <div class="ap-file-name" id="fileName"></div>
                            <input type="file" name="curriculo" accept=".pdf,.doc,.docx" required id="fileInput">
                        </div>
                        @error('curriculo')<div class="ap-error">{{ $message }}</div>@enderror
                    </div>

                    @if($vaga->perguntas->count() > 0)
                        <div class="ap-divider"></div>
                        <div class="ap-questions-title"><i class="bi bi-chat-square-text-fill"></i> Perguntas adicionais</div>

                        @foreach($vaga->perguntas as $p)
                        <div class="ap-group">
                            <label class="ap-label">
                                {{ $p->pergunta }}
                                @if($p->obrigatoria)<span class="req">*</span>@endif
                            </label>
                            @if($p->tipo === 'select')
                                <select name="resposta_{{ $p->id }}" class="ap-input" {{ $p->obrigatoria ? 'required' : '' }}>
                                    <option value="">Selecione...</option>
                                    @foreach($p->opcoes ?? [] as $op)
                                        <option value="{{ $op }}" {{ old("resposta_{$p->id}") === $op ? 'selected' : '' }}>{{ $op }}</option>
                                    @endforeach
                                </select>
                            @elseif($p->tipo === 'textarea')
                                <textarea name="resposta_{{ $p->id }}" class="ap-input" rows="3" {{ $p->obrigatoria ? 'required' : '' }}>{{ old("resposta_{$p->id}") }}</textarea>
                            @else
                                <input type="text" name="resposta_{{ $p->id }}" class="ap-input" value="{{ old("resposta_{$p->id}") }}" {{ $p->obrigatoria ? 'required' : '' }}>
                            @endif
                            @error("resposta_{$p->id}")<div class="ap-error">{{ $message }}</div>@enderror
                        </div>
                        @endforeach
                    @endif

                    <button type="submit" class="ap-submit">
                        <i class="bi bi-send-fill"></i> Enviar candidatura
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="ap-footer">
        <p>&copy; {{ date('Y') }} JPC Construções & Incorporações. Todos os direitos reservados.</p>
    </div>

    <script>
    var fileInput = document.getElementById('fileInput');
    var fileArea = document.getElementById('fileArea');
    var fileName = document.getElementById('fileName');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length) {
                fileName.innerHTML = '<i class="bi bi-file-earmark-check-fill"></i> ' + this.files[0].name;
                fileName.style.display = 'block';
            }
        });
        fileArea.addEventListener('dragover', function(e) { e.preventDefault(); fileArea.classList.add('dragover'); });
        fileArea.addEventListener('dragleave', function() { fileArea.classList.remove('dragover'); });
        fileArea.addEventListener('drop', function(e) {
            e.preventDefault();
            fileArea.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        });
    }

    // Máscara de telefone
    var telInput = document.querySelector('input[name="telefone"]');
    if (telInput) {
        telInput.addEventListener('input', function() {
            var v = this.value.replace(/\D/g, '').substring(0, 11);
            if (v.length > 6) v = '(' + v.substring(0,2) + ') ' + v.substring(2,7) + '-' + v.substring(7);
            else if (v.length > 2) v = '(' + v.substring(0,2) + ') ' + v.substring(2);
            else if (v.length > 0) v = '(' + v;
            this.value = v;
        });
    }
    </script>
</body>
</html>
