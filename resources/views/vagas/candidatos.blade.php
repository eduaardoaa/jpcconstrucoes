@extends('layouts.app')

@section('title', 'Candidatos - ' . $vaga->titulo)
@section('pageTitle', 'Candidatos')
@section('pageDescription', $vaga->titulo)

@section('content')
<style>
.vc-back{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:16px;text-decoration:none;transition:color .2s}
.vc-back:hover{color:#e2e8f0}

.vc-info-bar{display:flex;align-items:center;gap:14px;padding:16px 20px;background:rgba(59,130,246,.06);border:1px solid rgba(59,130,246,.14);border-radius:14px;margin-bottom:18px;flex-wrap:wrap}
.vc-info-bar .vg-stat{font-size:13px}
.vc-info-link{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);color:#86efac;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;margin-left:auto}
.vc-info-link:hover{background:rgba(34,197,94,.16)}

.vc-filters{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}
.vc-filter-btn{padding:7px 14px;border-radius:999px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid rgba(148,163,184,.14);background:rgba(255,255,255,.03);color:#94a3b8;transition:all .2s}
.vc-filter-btn:hover,.vc-filter-btn.active{background:rgba(59,130,246,.12);border-color:rgba(59,130,246,.25);color:#93c5fd}

.vc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px}

.vc-card{background:linear-gradient(180deg,rgba(18,28,42,.98),rgba(15,23,34,.98));border:1px solid rgba(148,163,184,.12);border-radius:18px;overflow:hidden;transition:transform .2s,border-color .2s}
.vc-card:hover{transform:translateY(-2px);border-color:rgba(59,130,246,.2)}

.vc-card-top{padding:18px 18px 12px;display:flex;align-items:center;gap:12px}
.vc-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,rgba(59,130,246,.22),rgba(59,130,246,.08));border:1px solid rgba(59,130,246,.25);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;color:#93c5fd;flex-shrink:0;text-transform:uppercase}
.vc-name{font-size:15px;font-weight:700;color:#f8fafc}
.vc-contact{font-size:12px;color:#94a3b8;margin-top:2px}
.vc-contact a{color:#86efac;text-decoration:none}
.vc-contact a:hover{text-decoration:underline}

.vc-card-status{margin-left:auto;display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700}
.vc-card-status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.vc-st-nova{background:rgba(56,189,248,.12);color:#7dd3fc;border:1px solid rgba(56,189,248,.2)}
.vc-st-analisando{background:rgba(245,158,11,.12);color:#fcd34d;border:1px solid rgba(245,158,11,.2)}
.vc-st-aprovada{background:rgba(34,197,94,.12);color:#86efac;border:1px solid rgba(34,197,94,.2)}
.vc-st-reprovada{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}

.vc-respostas{padding:0 18px 14px}
.vc-resp-item{margin-bottom:10px}
.vc-resp-q{font-size:11.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px}
.vc-resp-a{font-size:13.5px;color:#e2e8f0;line-height:1.4}

.vc-card-meta{padding:12px 18px;border-top:1px solid rgba(148,163,184,.08);display:flex;gap:10px;flex-wrap:wrap;font-size:12px;color:#64748b}
.vc-card-meta i{font-size:13px}

.vc-card-actions{padding:12px 18px;border-top:1px solid rgba(148,163,184,.08);display:flex;gap:6px;flex-wrap:wrap}

.vc-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .2s;text-decoration:none}
.vc-btn:hover{transform:translateY(-1px)}
.vc-btn-wpp{background:rgba(37,211,102,.12);border-color:rgba(37,211,102,.22);color:#86efac}
.vc-btn-wpp:hover{background:rgba(37,211,102,.18)}
.vc-btn-dl{background:rgba(59,130,246,.1);border-color:rgba(59,130,246,.2);color:#93c5fd}
.vc-btn-dl:hover{background:rgba(59,130,246,.16)}
.vc-btn-dark{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08);color:#e2e8f0}
.vc-btn-dark:hover{background:rgba(255,255,255,.07)}

/* Status Modal */
.vs-label{display:block;margin-bottom:6px;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8}
.vs-input{width:100%;min-height:44px;padding:10px 14px;border-radius:10px;border:1px solid rgba(148,163,184,.2);background:#0b1220;color:#f8fafc;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s;resize:vertical}
.vs-input:focus{border-color:rgba(59,130,246,.4);box-shadow:0 0 0 3px rgba(59,130,246,.12)}
.vs-input::placeholder{color:#475569}

.vs-status-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:18px}
.vs-status-opt{display:flex;align-items:center;gap:10px;padding:14px;border-radius:12px;background:rgba(255,255,255,.02);border:2px solid rgba(148,163,184,.1);cursor:pointer;transition:all .2s;position:relative}
.vs-status-opt:hover{border-color:rgba(148,163,184,.25);background:rgba(255,255,255,.04)}
.vs-status-opt.selected{border-color:var(--sel-color);background:var(--sel-bg)}
.vs-status-opt input{position:absolute;opacity:0;pointer-events:none}
.vs-status-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.vs-status-info{min-width:0}
.vs-status-name{font-size:13.5px;font-weight:700;color:#e2e8f0}
.vs-status-desc{font-size:11px;color:#64748b;margin-top:1px}
.vs-status-opt.selected .vs-status-name{color:#f8fafc}
.vs-status-opt.selected .vs-status-desc{color:#94a3b8}

.vs-status-opt[data-status='nova']{--sel-color:rgba(56,189,248,.5);--sel-bg:rgba(56,189,248,.08)}
.vs-status-opt[data-status='nova'] .vs-status-dot{background:#38bdf8}
.vs-status-opt[data-status='analisando']{--sel-color:rgba(245,158,11,.5);--sel-bg:rgba(245,158,11,.08)}
.vs-status-opt[data-status='analisando'] .vs-status-dot{background:#f59e0b}
.vs-status-opt[data-status='aprovada']{--sel-color:rgba(34,197,94,.5);--sel-bg:rgba(34,197,94,.08)}
.vs-status-opt[data-status='aprovada'] .vs-status-dot{background:#22c55e}
.vs-status-opt[data-status='reprovada']{--sel-color:rgba(239,68,68,.5);--sel-bg:rgba(239,68,68,.08)}
.vs-status-opt[data-status='reprovada'] .vs-status-dot{background:#ef4444}

.vs-cand-info{display:flex;align-items:center;gap:10px;padding:14px;border-radius:12px;background:rgba(255,255,255,.02);border:1px solid rgba(148,163,184,.08);margin-bottom:18px}
.vs-cand-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,rgba(59,130,246,.22),rgba(59,130,246,.08));border:1px solid rgba(59,130,246,.25);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#93c5fd;flex-shrink:0;text-transform:uppercase}
.vs-cand-name{font-size:14px;font-weight:700;color:#f8fafc}
.vs-cand-sub{font-size:11.5px;color:#64748b;margin-top:1px}

.vc-empty{text-align:center;padding:50px 20px;color:#64748b}
.vc-empty i{font-size:42px;color:rgba(148,163,184,.2);display:block;margin-bottom:12px}

.vc-ai-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.1);
    z-index: 2;
}
.vc-ai-score-high { background: linear-gradient(135deg, #059669, #10b981); color: #fff; }
.vc-ai-score-mid { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; }
.vc-ai-score-low { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; }
.vc-ai-processing { background: rgba(148, 163, 184, 0.1); color: #94a3b8; animation: pulse 2s infinite; }

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.vc-ai-analysis {
    margin: 0 18px 14px;
    padding: 12px;
    background: rgba(59, 130, 246, 0.05);
    border: 1px solid rgba(59, 130, 246, 0.12);
    border-radius: 12px;
}
.vc-ai-title {
    font-size: 11px;
    font-weight: 800;
    color: #93c5fd;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.vc-ai-text {
    font-size: 12.5px;
    color: #cbd5e1;
    line-height: 1.5;
}

@media(max-width:640px){.vc-grid{grid-template-columns:1fr}}

    html {
        scroll-behavior: smooth;
    }

    .ranking-item:hover div {
        background: rgba(251, 191, 36, 0.1) !important;
        border-color: rgba(251, 191, 36, 0.4) !important;
        transform: translateY(-2px);
    }

    .vc-card-top {
        position: relative;
        padding: 20px 18px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 80px;
    }

    .vc-ai-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.1);
        z-index: 2;
    }

    .vc-card-status {
        margin-left: auto;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        position: relative;
        top: 15px; /* Move status down to avoid overlap */
    }
</style>

@if(session('success'))
    <div class="alert-success-box"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif

<a href="{{ route('vagas.index') }}" class="vc-back"><i class="bi bi-arrow-left"></i> Voltar para vagas</a>

<div class="vc-info-bar">
    <div class="vg-stat"><i class="bi bi-briefcase-fill" style="color:#3b82f6"></i> <strong>{{ $vaga->titulo }}</strong></div>
    <div class="vg-stat"><i class="bi bi-people-fill"></i> <strong>{{ $vaga->candidaturas->count() }}</strong> candidato(s)</div>
    @if($vaga->local)<div class="vg-stat"><i class="bi bi-geo-alt"></i> {{ $vaga->local }}</div>@endif
    <button class="vc-info-link" onclick="navigator.clipboard.writeText('{{ $vaga->linkPublico() }}');this.innerHTML='<i class=\'bi bi-check\'></i> Copiado!'">
        <i class="bi bi-link-45deg"></i> Copiar link
    </button>
</div>

{{-- Ranking Top Candidatos --}}
@php
    $topCandidatos = $vaga->candidaturas->where('ai_status', 'completed')->sortByDesc('ai_score')->take(3);
@endphp

@if($topCandidatos->count() > 0)
<div class="ranking-section" style="margin-bottom: 24px;">
    <div class="vc-ai-title" style="font-size: 13px; margin-bottom: 12px; color: #fcd34d;">
        <i class="bi bi-trophy-fill"></i> Ranking de Excelência (IA)
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        @foreach($topCandidatos as $rank => $tc)
            <a href="#cand-{{ $tc->id }}" class="ranking-item" style="text-decoration:none; flex: 1; min-width: 200px;">
                <div style="display: flex; align-items: center; gap: 10px; padding: 12px; background: rgba(251, 191, 36, 0.05); border: 1px solid rgba(251, 191, 36, 0.2); border-radius: 12px; transition: all 0.2s;">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;">
                        {{ $loop->iteration }}º
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 13px; font-weight: 700; color: #f8fafc;">{{ $tc->nome }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">Match Score: {{ $tc->ai_score }}%</div>
                    </div>
                    <i class="bi bi-chevron-right" style="color: #64748b;"></i>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

<div class="vc-filters">
    <button class="vc-filter-btn active" onclick="filterCards('all',this)">Todos</button>
    <button class="vc-filter-btn" onclick="filterCards('nova',this)">Novos</button>
    <button class="vc-filter-btn" onclick="filterCards('analisando',this)">Analisando</button>
    <button class="vc-filter-btn" onclick="filterCards('aprovada',this)">Aprovados</button>
    <button class="vc-filter-btn" onclick="filterCards('reprovada',this)">Reprovados</button>
</div>

@if($vaga->candidaturas->isEmpty())
    <div class="vc-empty">
        <i class="bi bi-inbox"></i>
        <h3 style="color:#e2e8f0;font-weight:600;font-size:15px;margin-bottom:4px">Nenhum candidato ainda</h3>
        <p>Compartilhe o link da vaga para receber candidaturas.</p>
    </div>
@else
    <div class="vc-grid">
        @foreach($vaga->candidaturas as $c)
            <div class="vc-card" data-status="{{ $c->status }}" id="cand-{{ $c->id }}" style="scroll-margin-top: 20px;">
                <div class="vc-card-top">
                    @if($c->ai_status === 'completed')
                        @php
                            $scoreClass = $c->ai_score >= 80 ? 'vc-ai-score-high' : ($c->ai_score >= 50 ? 'vc-ai-score-mid' : 'vc-ai-score-low');
                        @endphp
                        <div class="vc-ai-badge {{ $scoreClass }}" title="Match Score da IA">
                            <i class="bi bi-robot"></i> {{ $c->ai_score }}%
                        </div>
                    @elseif($c->ai_status === 'processing' || $c->ai_status === 'pending')
                        <div class="vc-ai-badge vc-ai-processing">
                            <i class="bi bi-robot"></i> Analisando...
                        </div>
                    @elseif($c->ai_status === 'failed')
                        <div class="vc-ai-badge" style="background:rgba(239,68,68,.1);color:#fca5a5;border-color:rgba(239,68,68,.2)">
                            <i class="bi bi-exclamation-triangle"></i> Falhou
                        </div>
                    @endif

                    <div class="vc-avatar">{{ strtoupper(mb_substr($c->nome, 0, 2)) }}</div>
                    <div style="max-width: 160px;">
                        <div class="vc-name">{{ $c->nome }}</div>
                        <div class="vc-contact">
                            {{ $c->telefone }}
                        </div>
                    </div>
                    <span class="vc-card-status vc-st-{{ $c->status }}">
                        {{ ucfirst($c->status) }}
                    </span>
                </div>

                @if($c->ai_status === 'completed')
                    <div class="vc-ai-analysis">
                        <div class="vc-ai-title"><i class="bi bi-stars"></i> Análise do Recrutador IA</div>
                        <div class="vc-ai-text" style="margin-bottom:8px">{{ $c->ai_summary }}</div>
                        
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <div class="vc-ai-title" style="color:#22c55e; font-size:10px;"><i class="bi bi-plus-circle"></i> Pontos Fortes</div>
                                <div class="vc-ai-text" style="font-size:11px;">{{ $c->ai_pontos_fortes ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="vc-ai-title" style="color:#f43f5e; font-size:10px;"><i class="bi bi-dash-circle"></i> Pontos a Melhorar</div>
                                <div class="vc-ai-text" style="font-size:11px;">{{ $c->ai_pontos_fracos ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                @endif



                @if($c->respostas && count($c->respostas) > 0)
                    <div class="vc-respostas">
                        @foreach($vaga->perguntas as $p)
                            @if(isset($c->respostas[$p->id]))
                                <div class="vc-resp-item">
                                    <div class="vc-resp-q">{{ $p->pergunta }}</div>
                                    <div class="vc-resp-a">{{ $c->respostas[$p->id] ?: '—' }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if($c->observacoes)
                    <div style="padding:0 18px 12px">
                        <div class="vc-resp-q">Observações internas</div>
                        <div class="vc-resp-a" style="font-style:italic;color:#fcd34d">{{ $c->observacoes }}</div>
                    </div>
                @endif

                <div class="vc-card-meta">
                    <span><i class="bi bi-clock"></i> {{ $c->created_at->format('d/m/Y H:i') }}</span>
                    <span><i class="bi bi-file-earmark"></i> {{ $c->curriculo_nome_original }}</span>
                </div>

                <div class="vc-card-actions">
                    <a href="{{ $c->linkWhatsapp('Olá ' . explode(' ', $c->nome)[0] . ', tudo bem? Vi sua candidatura para a vaga de ' . $vaga->titulo . '.') }}" target="_blank" class="vc-btn vc-btn-wpp">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                    <a href="{{ route('vagas.candidatura.curriculo', $c) }}" target="_blank" class="vc-btn vc-btn-dl">
                        <i class="bi bi-eye"></i> Currículo
                    </a>
                    <button class="vc-btn vc-btn-dark" onclick="openStatusModal({{ $c->id }},'{{ $c->status }}','{{ addslashes($c->observacoes) }}','{{ addslashes($c->nome) }}')">
                        <i class="bi bi-arrow-repeat"></i> Status
                    </button>
                    <form method="POST" action="{{ route('vagas.candidatura.reanalisar', $c) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="vc-btn vc-btn-dark" title="Forçar reanálise por IA">
                            <i class="bi bi-stars"></i> Reanalisar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Modal de status --}}
<div class="custom-modal" id="statusModal" role="dialog">
    <div class="custom-modal-backdrop" onclick="closeStatusModal()"></div>
    <div class="custom-modal-dialog" style="max-width:480px">
        <div class="custom-modal-header">
            <div>
                <h3>Alterar Status</h3>
                <p>Defina o andamento desta candidatura.</p>
            </div>
            <button class="custom-modal-close" onclick="closeStatusModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="custom-modal-body">
            <div class="vs-cand-info" id="vsCandInfo">
                <div class="vs-cand-avatar" id="vsCandAvatar"></div>
                <div>
                    <div class="vs-cand-name" id="vsCandName"></div>
                    <div class="vs-cand-sub">Candidato(a)</div>
                </div>
            </div>

            <form method="POST" id="statusForm">
                @csrf @method('PATCH')

                <div class="vs-label">Selecione o status</div>
                <div class="vs-status-grid">
                    <label class="vs-status-opt" data-status="nova" onclick="selectStatus(this)">
                        <input type="radio" name="status" value="nova">
                        <div class="vs-status-dot"></div>
                        <div class="vs-status-info">
                            <div class="vs-status-name">Nova</div>
                            <div class="vs-status-desc">Ainda não avaliada</div>
                        </div>
                    </label>
                    <label class="vs-status-opt" data-status="analisando" onclick="selectStatus(this)">
                        <input type="radio" name="status" value="analisando">
                        <div class="vs-status-dot"></div>
                        <div class="vs-status-info">
                            <div class="vs-status-name">Analisando</div>
                            <div class="vs-status-desc">Em avaliação</div>
                        </div>
                    </label>
                    <label class="vs-status-opt" data-status="aprovada" onclick="selectStatus(this)">
                        <input type="radio" name="status" value="aprovada">
                        <div class="vs-status-dot"></div>
                        <div class="vs-status-info">
                            <div class="vs-status-name">Aprovada</div>
                            <div class="vs-status-desc">Candidato aprovado</div>
                        </div>
                    </label>
                    <label class="vs-status-opt" data-status="reprovada" onclick="selectStatus(this)">
                        <input type="radio" name="status" value="reprovada">
                        <div class="vs-status-dot"></div>
                        <div class="vs-status-info">
                            <div class="vs-status-name">Reprovada</div>
                            <div class="vs-status-desc">Não selecionado</div>
                        </div>
                    </label>
                </div>

                <div class="vs-label">Observações internas</div>
                <textarea name="observacoes" id="statusObs" class="vs-input" rows="3" placeholder="Notas sobre o candidato..."></textarea>

                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px;padding-top:16px;border-top:1px solid rgba(148,163,184,.1)">
                    <button type="button" class="vc-btn vc-btn-dark" onclick="closeStatusModal()">Cancelar</button>
                    <button type="submit" class="vc-btn" style="background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,.2)"><i class="bi bi-check-lg"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectStatus(el) {
    document.querySelectorAll('.vs-status-opt').forEach(function(o) { o.classList.remove('selected'); });
    el.classList.add('selected');
    el.querySelector('input').checked = true;
}

function openStatusModal(id, status, obs, nome) {
    document.getElementById('statusForm').action = '/vagas/candidaturas/' + id + '/status';
    document.getElementById('statusObs').value = obs || '';
    document.getElementById('vsCandName').textContent = nome || 'Candidato';
    document.getElementById('vsCandAvatar').textContent = (nome || 'C').substring(0, 2).toUpperCase();

    document.querySelectorAll('.vs-status-opt').forEach(function(o) {
        o.classList.remove('selected');
        var radio = o.querySelector('input');
        if (radio.value === status) {
            o.classList.add('selected');
            radio.checked = true;
        }
    });

    var modal = document.getElementById('statusModal');
    modal.classList.add('is-open');
    document.body.classList.add('modal-open');
}

function closeStatusModal() {
    var modal = document.getElementById('statusModal');
    modal.classList.remove('is-open');
    document.body.classList.remove('modal-open');
}

function filterCards(status, btn) {
    document.querySelectorAll('.vc-filter-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.vc-card').forEach(function(c) {
        c.style.display = (status === 'all' || c.dataset.status === status) ? '' : 'none';
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeStatusModal();
});
</script>
@endsection
