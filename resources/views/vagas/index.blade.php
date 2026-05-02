@extends('layouts.app')

@section('title', 'Gerenciamento de Vagas')
@section('pageTitle', 'Vagas e Currículos')
@section('pageDescription', 'Crie vagas, compartilhe o link e receba candidaturas.')

@section('content')
<style>
.vg-head{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:18px;flex-wrap:wrap}
.vg-head h2{font-size:19px;font-weight:700;color:#f8fafc;display:flex;align-items:center;gap:10px}
.vg-head h2 i{font-size:22px;color:#3b82f6}
.vg-head p{font-size:13px;color:#94a3b8;margin-top:2px}
.vg-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:18px}
.vg-card{background:linear-gradient(180deg,rgba(18,28,42,.98),rgba(15,23,34,.98));border:1px solid rgba(148,163,184,.14);border-radius:20px;overflow:hidden;transition:transform .2s,border-color .2s,box-shadow .2s}
.vg-card:hover{transform:translateY(-2px);border-color:rgba(59,130,246,.25);box-shadow:0 12px 32px rgba(0,0,0,.3)}
.vg-card-top{padding:20px 20px 14px;display:flex;justify-content:space-between;align-items:flex-start;gap:12px}
.vg-card-title{font-size:17px;font-weight:700;color:#f8fafc;margin-bottom:4px}
.vg-card-meta{font-size:12.5px;color:#94a3b8;display:flex;flex-wrap:wrap;gap:8px;align-items:center}
.vg-card-meta i{font-size:11px}
.vg-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:999px;font-size:11.5px;font-weight:700;line-height:1}
.vg-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.vg-badge-open{background:rgba(34,197,94,.12);color:#86efac;border:1px solid rgba(34,197,94,.2)}
.vg-badge-closed{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
.vg-card-body{padding:0 20px 16px}
.vg-card-desc{font-size:13.5px;color:#94a3b8;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.vg-card-stats{display:flex;gap:14px;padding:14px 20px;border-top:1px solid rgba(148,163,184,.1);background:rgba(255,255,255,.01)}
.vg-stat{display:flex;align-items:center;gap:6px;font-size:12.5px;color:#94a3b8}
.vg-stat i{font-size:14px;color:#64748b}
.vg-stat strong{color:#e2e8f0;font-weight:700}
.vg-card-actions{display:flex;gap:6px;padding:14px 20px;border-top:1px solid rgba(148,163,184,.1);flex-wrap:wrap}
.vg-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 14px;border-radius:10px;font-size:12.5px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .2s;text-decoration:none}
.vg-btn:hover{transform:translateY(-1px)}
.vg-btn-primary{background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,.2)}
.vg-btn-dark{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08);color:#e2e8f0}
.vg-btn-dark:hover{background:rgba(255,255,255,.07)}
.vg-btn-copy{background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.2);color:#86efac}
.vg-btn-copy:hover{background:rgba(34,197,94,.16)}
.vg-btn-danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.2);color:#fca5a5}
.vg-btn-danger:hover{background:rgba(239,68,68,.16)}
.vg-btn-warning{background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.2);color:#fcd34d}
.vg-btn-warning:hover{background:rgba(245,158,11,.16)}
.vg-btn-icon{width:34px;height:34px;padding:0;border-radius:8px}

.vm-form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
.vm-form-full{grid-column:1/-1}
.vm-label{display:block;margin-bottom:6px;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8}
.vm-input{width:100%;min-height:44px;padding:10px 14px;border-radius:10px;border:1px solid rgba(148,163,184,.2);background:#0b1220;color:#f8fafc;font-size:14px;outline:none;transition:border-color .2s,box-shadow .2s;font-family:inherit}
.vm-input:focus{border-color:rgba(59,130,246,.4);box-shadow:0 0 0 3px rgba(59,130,246,.12)}
.vm-input::placeholder{color:#475569}
textarea.vm-input{min-height:80px;resize:vertical}
select.vm-input{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px}
.vm-divider{grid-column:1/-1;height:1px;background:rgba(148,163,184,.1);margin:6px 0}
.vm-perguntas-wrap{grid-column:1/-1;margin-top:4px}
.vm-perguntas-title{font-size:14px;font-weight:700;color:#e2e8f0;margin-bottom:10px;display:flex;align-items:center;gap:8px}
.vm-pergunta-item{background:#0b1220;border:1px solid rgba(148,163,184,.12);border-radius:12px;padding:14px;margin-bottom:10px}
.vm-pergunta-row{display:grid;grid-template-columns:1fr auto auto;gap:10px;align-items:end}
.vm-pergunta-item .vm-input{min-height:38px;font-size:13px}
.vm-pergunta-opcoes{margin-top:10px;display:none}
.vm-pergunta-opcoes.show{display:block}
.vm-pergunta-opcoes .vm-hint{font-size:11px;color:#64748b;margin-top:4px}
.vm-pergunta-remove{width:34px;height:34px;border-radius:8px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#fca5a5;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s}
.vm-pergunta-remove:hover{background:rgba(239,68,68,.2)}
.vm-footer{padding:16px 24px;border-top:1px solid rgba(148,163,184,.12);display:flex;justify-content:flex-end;gap:10px}
.vg-empty{text-align:center;padding:60px 20px;color:#64748b}
.vg-empty i{font-size:48px;color:rgba(148,163,184,.2);display:block;margin-bottom:14px}
.vg-empty h3{font-size:16px;color:#e2e8f0;font-weight:600;margin-bottom:6px}
.modal-open{overflow:hidden}

/* ── Dashboard ── */
.vd-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
.vd-kpi{padding:18px;border-radius:16px;background:linear-gradient(180deg,rgba(18,28,42,.98),rgba(15,23,34,.98));border:1px solid rgba(148,163,184,.1);position:relative;overflow:hidden}
.vd-kpi::after{content:"";position:absolute;top:0;right:0;width:80px;height:80px;border-radius:50%;opacity:.06;transform:translate(20px,-20px)}
.vd-kpi:nth-child(1)::after{background:#3b82f6}
.vd-kpi:nth-child(2)::after{background:#22c55e}
.vd-kpi:nth-child(3)::after{background:#a855f7}
.vd-kpi:nth-child(4)::after{background:#f59e0b}
.vd-kpi-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;margin-bottom:10px}
.vd-kpi:nth-child(1) .vd-kpi-icon{background:rgba(59,130,246,.1);color:#3b82f6}
.vd-kpi:nth-child(2) .vd-kpi-icon{background:rgba(34,197,94,.1);color:#22c55e}
.vd-kpi:nth-child(3) .vd-kpi-icon{background:rgba(168,85,247,.1);color:#a855f7}
.vd-kpi:nth-child(4) .vd-kpi-icon{background:rgba(245,158,11,.1);color:#f59e0b}
.vd-kpi-value{font-size:26px;font-weight:800;color:#f8fafc;letter-spacing:-.02em}
.vd-kpi-label{font-size:12px;color:#64748b;font-weight:500;margin-top:2px}
.vd-kpi-suffix{font-size:14px;font-weight:600;color:#94a3b8;margin-left:2px}

.vd-charts{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:22px}
.vd-chart-card{padding:20px;border-radius:16px;background:linear-gradient(180deg,rgba(18,28,42,.98),rgba(15,23,34,.98));border:1px solid rgba(148,163,184,.1)}
.vd-chart-title{font-size:13px;font-weight:700;color:#e2e8f0;margin-bottom:14px;display:flex;align-items:center;gap:7px}
.vd-chart-title i{color:#3b82f6;font-size:14px}
.vd-chart-wrap{position:relative;height:200px}

.vd-bottom-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:22px}
.vd-rank-card{padding:20px;border-radius:16px;background:linear-gradient(180deg,rgba(18,28,42,.98),rgba(15,23,34,.98));border:1px solid rgba(148,163,184,.1)}
.vd-rank-item{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid rgba(148,163,184,.06)}
.vd-rank-item:last-child{border-bottom:none}
.vd-rank-pos{width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#f8fafc;flex-shrink:0}
.vd-rank-pos.r1{background:linear-gradient(135deg,#f59e0b,#d97706)}
.vd-rank-pos.r2{background:rgba(148,163,184,.15);color:#94a3b8}
.vd-rank-pos.r3{background:rgba(180,120,80,.2);color:#d4a574}
.vd-rank-pos.rn{background:rgba(148,163,184,.08);color:#64748b}
.vd-rank-name{flex:1;font-size:13px;color:#e2e8f0;font-weight:500;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.vd-rank-count{font-size:13px;font-weight:700;color:#93c5fd}
.vd-rank-bar{flex:1;display:flex;align-items:center;gap:10px}
.vd-rank-bar-bg{flex:1;height:6px;border-radius:3px;background:rgba(148,163,184,.08);overflow:hidden}
.vd-rank-bar-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,#3b82f6,#60a5fa);transition:width .5s ease}

@media(max-width:900px){
    .vd-kpis{grid-template-columns:repeat(2,1fr)}
    .vd-charts,.vd-bottom-row{grid-template-columns:1fr}
}
@media(max-width:640px){
    .vg-grid{grid-template-columns:1fr}
    .vm-form-grid{grid-template-columns:1fr}
    .vg-head{flex-direction:column;align-items:stretch}
    .custom-modal-dialog{width:calc(100% - 12px);max-height:92vh;border-radius:18px}
    .custom-modal-header{padding:18px 16px 14px}
    .custom-modal-body{padding:16px}
    .vd-kpis{grid-template-columns:1fr 1fr}
}
</style>

@if(session('success'))
    <div class="alert-success-box"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert-error-box"><i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}</div>
@endif

{{-- ═══ DASHBOARD ANALYTICS ═══ --}}
<div class="vd-kpis">
    <div class="vd-kpi">
        <div class="vd-kpi-icon"><i class="bi bi-people-fill"></i></div>
        <div class="vd-kpi-value">{{ $totalCandidatos }}</div>
        <div class="vd-kpi-label">Total de candidatos</div>
    </div>
    <div class="vd-kpi">
        <div class="vd-kpi-icon"><i class="bi bi-person-plus-fill"></i></div>
        <div class="vd-kpi-value">{{ $candidatosHoje }}</div>
        <div class="vd-kpi-label">Candidatos hoje</div>
    </div>
    <div class="vd-kpi">
        <div class="vd-kpi-icon"><i class="bi bi-briefcase-fill"></i></div>
        <div class="vd-kpi-value">{{ $vagasAbertas }}</div>
        <div class="vd-kpi-label">Vagas abertas</div>
    </div>
    <div class="vd-kpi">
        <div class="vd-kpi-icon"><i class="bi bi-trophy-fill"></i></div>
        <div class="vd-kpi-value">{{ $taxaAprovacao }}<span class="vd-kpi-suffix">%</span></div>
        <div class="vd-kpi-label">Taxa de aprovação</div>
    </div>
</div>

<div class="vd-charts">
    <div class="vd-chart-card">
        <div class="vd-chart-title"><i class="bi bi-graph-up"></i> Candidaturas — Últimos 14 dias</div>
        <div class="vd-chart-wrap"><canvas id="chartDias"></canvas></div>
    </div>
    <div class="vd-chart-card">
        <div class="vd-chart-title"><i class="bi bi-pie-chart-fill"></i> Distribuição por status</div>
        <div class="vd-chart-wrap"><canvas id="chartStatus"></canvas></div>
    </div>
</div>

@if($topVagas->count() > 0)
<div class="vd-bottom-row">
    <div class="vd-rank-card">
        <div class="vd-chart-title"><i class="bi bi-bar-chart-fill"></i> Top vagas — Mais candidatos</div>
        @php $maxTop = $topVagas->max('total') ?: 1; @endphp
        @foreach($topVagas as $i => $tv)
        <div class="vd-rank-item">
            <div class="vd-rank-pos {{ $i === 0 ? 'r1' : ($i === 1 ? 'r2' : ($i === 2 ? 'r3' : 'rn')) }}">{{ $i + 1 }}</div>
            <div class="vd-rank-bar">
                <div class="vd-rank-name">{{ $tv['titulo'] }}</div>
                <div class="vd-rank-bar-bg"><div class="vd-rank-bar-fill" style="width:{{ ($tv['total'] / $maxTop) * 100 }}%"></div></div>
            </div>
            <div class="vd-rank-count">{{ $tv['total'] }}</div>
        </div>
        @endforeach
    </div>
    <div class="vd-chart-card">
        <div class="vd-chart-title"><i class="bi bi-graph-up-arrow"></i> Top vagas — Gráfico</div>
        <div class="vd-chart-wrap"><canvas id="chartTopVagas"></canvas></div>
    </div>
</div>
@endif

{{-- ═══ HEADER VAGAS ═══ --}}
<div class="vg-head">
    <div>
        <h2><i class="bi bi-briefcase-fill"></i> Vagas</h2>
        <p>{{ $vagas->count() }} vaga(s) cadastrada(s)</p>
    </div>
    <div>
        <button class="vg-btn vg-btn-primary" onclick="openVagaCriarModal()">
            <i class="bi bi-plus-lg"></i> Nova Vaga
        </button>
    </div>
</div>

@if($vagas->isEmpty())
    <div class="vg-empty">
        <i class="bi bi-briefcase"></i>
        <h3>Nenhuma vaga cadastrada</h3>
        <p>Crie sua primeira vaga e compartilhe o link com os candidatos.</p>
    </div>
@else
    <div class="vg-grid">
        @foreach($vagas as $vaga)
            <div class="vg-card">
                <div class="vg-card-top">
                    <div>
                        <div class="vg-card-title">{{ $vaga->titulo }}</div>
                        <div class="vg-card-meta">
                            @if($vaga->local)<span><i class="bi bi-geo-alt-fill"></i> {{ $vaga->local }}</span>@endif
                            @if($vaga->tipo_contrato)<span><i class="bi bi-tag-fill"></i> {{ $vaga->tipo_contrato }}</span>@endif
                            @if($vaga->salario)<span><i class="bi bi-currency-dollar"></i> {{ $vaga->salario }}</span>@endif
                        </div>
                    </div>
                    <span class="vg-badge {{ $vaga->isAberta() ? 'vg-badge-open' : 'vg-badge-closed' }}">
                        {{ $vaga->isAberta() ? 'Aberta' : 'Fechada' }}
                    </span>
                </div>
                @if($vaga->descricao)
                    <div class="vg-card-body"><div class="vg-card-desc">{{ $vaga->descricao }}</div></div>
                @endif
                <div class="vg-card-stats">
                    <div class="vg-stat"><i class="bi bi-people-fill"></i> <strong>{{ $vaga->candidaturas_count }}</strong> candidato(s)</div>
                    @if($vaga->data_limite)<div class="vg-stat"><i class="bi bi-calendar-event"></i> Até {{ $vaga->data_limite->format('d/m/Y') }}</div>@endif
                    <div class="vg-stat"><i class="bi bi-clock"></i> {{ $vaga->created_at->diffForHumans() }}</div>
                </div>
                <div class="vg-card-actions">
                    <a href="{{ route('vagas.candidatos', $vaga) }}" class="vg-btn vg-btn-primary"><i class="bi bi-people"></i> Candidatos</a>
                    <button class="vg-btn vg-btn-copy" onclick="vagaCopyLink('{{ $vaga->linkPublico() }}')"><i class="bi bi-link-45deg"></i> Copiar Link</button>
                    <button class="vg-btn vg-btn-dark" onclick="openVagaEditModal({{ $vaga->id }})"><i class="bi bi-pencil"></i></button>
                    <form method="POST" action="{{ route('vagas.toggle-status', $vaga) }}" style="margin:0">@csrf @method('PATCH')
                        <button class="vg-btn {{ $vaga->isAberta() ? 'vg-btn-warning' : 'vg-btn-copy' }} vg-btn-icon" title="{{ $vaga->isAberta() ? 'Fechar vaga' : 'Reabrir vaga' }}"><i class="bi {{ $vaga->isAberta() ? 'bi-pause-fill' : 'bi-play-fill' }}"></i></button>
                    </form>
                </div>

            </div>
        @endforeach
    </div>
@endif

{{-- MODAL CRIAR --}}
<div class="custom-modal" id="vagaCriarModal" role="dialog">
    <div class="custom-modal-backdrop" onclick="closeVagaCriarModal()"></div>
    <div class="custom-modal-dialog">
        <div class="custom-modal-header">
            <div style="display:flex; justify-content: space-between; align-items: center; width: 100%;">
                <div>
                    <h3>Criar Nova Vaga</h3>
                    <p>Preencha os detalhes para publicar a vaga.</p>
                </div>
                <div>
                    <input type="file" id="importImageInput" style="display:none;" accept="image/*" onchange="vagaProcessarImagem(this)">
                    <button type="button" class="vg-btn vg-btn-dark" onclick="document.getElementById('importImageInput').click()" id="btnImportarIA">
                        <i class="bi bi-stars"></i> Importar de Flyer/Imagem
                    </button>
                </div>
            </div>
            <button class="custom-modal-close" onclick="closeVagaCriarModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="custom-modal-body">
            <form method="POST" action="{{ route('vagas.store') }}">
                @csrf
                <div class="vm-form-grid">
                    <div class="vm-form-full">
                        <label class="vm-label">Título da vaga *</label>
                        <input type="text" name="titulo" class="vm-input" placeholder="Ex: Pedreiro Experiente" required>
                    </div>
                    <div>
                        <label class="vm-label">Local</label>
                        <input type="text" name="local" class="vm-input" placeholder="São Paulo - SP">
                    </div>
                    <div>
                        <label class="vm-label">Tipo de contrato</label>
                        <select name="tipo_contrato" class="vm-input">
                            <option value="">Selecione...</option>
                            <option value="CLT">CLT</option><option value="PJ">PJ</option>
                            <option value="Temporário">Temporário</option><option value="Estágio">Estágio</option>
                            <option value="Freelancer">Freelancer</option>
                        </select>
                    </div>
                    <div>
                        <label class="vm-label">Salário</label>
                        <input type="text" name="salario" class="vm-input" placeholder="A combinar / R$ 2.500">
                    </div>
                    <div>
                        <label class="vm-label">Data limite</label>
                        <input type="date" name="data_limite" class="vm-input">
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Descrição</label>
                        <textarea name="descricao" class="vm-input" rows="3" placeholder="O que o profissional fará no dia a dia..."></textarea>
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Requisitos</label>
                        <textarea name="requisitos" class="vm-input" rows="2" placeholder="Ex: CNH B, Experiência em obras, Formação em Engenharia..."></textarea>
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Diferenciais (Opcional)</label>
                        <textarea name="diferenciais" class="vm-input" rows="2" placeholder="Ex: Pós-graduação, Conhecimento em software específico..."></textarea>
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Benefícios</label>
                        <textarea name="beneficios" class="vm-input" rows="2" placeholder="Ex: Vale transporte, Plano de saúde..."></textarea>
                    </div>
                    <div class="vm-divider"></div>
                    <div class="vm-perguntas-wrap">
                        <div class="vm-perguntas-title"><i class="bi bi-chat-square-text-fill" style="color:#3b82f6"></i> Perguntas para o candidato</div>
                        <div id="perguntasCriar"></div>
                        <button type="button" class="vg-btn vg-btn-dark" onclick="vagaAddPergunta('perguntasCriar','criar')" style="margin-top:6px"><i class="bi bi-plus"></i> Adicionar pergunta</button>
                    </div>
                </div>
                <div class="vm-footer" style="padding:16px 0 0;border-top:1px solid rgba(148,163,184,.12);margin-top:16px">
                    <button type="button" class="vg-btn vg-btn-dark" onclick="closeVagaCriarModal()">Cancelar</button>
                    <button type="submit" class="vg-btn vg-btn-primary">Criar Vaga</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAIS EDITAR --}}
@foreach($vagas as $vaga)
<div class="custom-modal" id="vagaEditModal{{ $vaga->id }}" role="dialog">
    <div class="custom-modal-backdrop" onclick="closeVagaEditModal({{ $vaga->id }})"></div>
    <div class="custom-modal-dialog">
        <div class="custom-modal-header">
            <div><h3>Editar Vaga</h3><p>Altere os dados e perguntas da vaga.</p></div>
            <button class="custom-modal-close" onclick="closeVagaEditModal({{ $vaga->id }})"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="custom-modal-body">
            <form method="POST" action="{{ route('vagas.update', $vaga) }}">
                @csrf @method('PUT')
                <div class="vm-form-grid">
                    <div class="vm-form-full">
                        <label class="vm-label">Título *</label>
                        <input type="text" name="titulo" class="vm-input" value="{{ $vaga->titulo }}" required>
                    </div>
                    <div>
                        <label class="vm-label">Local</label>
                        <input type="text" name="local" class="vm-input" value="{{ $vaga->local }}">
                    </div>
                    <div>
                        <label class="vm-label">Tipo de contrato</label>
                        <select name="tipo_contrato" class="vm-input">
                            <option value="">Selecione...</option>
                            @foreach(['CLT','PJ','Temporário','Estágio','Freelancer'] as $tc)
                                <option value="{{ $tc }}" {{ $vaga->tipo_contrato === $tc ? 'selected' : '' }}>{{ $tc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="vm-label">Salário</label>
                        <input type="text" name="salario" class="vm-input" value="{{ $vaga->salario }}">
                    </div>
                    <div>
                        <label class="vm-label">Data limite</label>
                        <input type="date" name="data_limite" class="vm-input" value="{{ $vaga->data_limite?->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="vm-label">Status</label>
                        <select name="status" class="vm-input">
                            <option value="aberta" {{ $vaga->status === 'aberta' ? 'selected' : '' }}>Aberta</option>
                            <option value="fechada" {{ $vaga->status === 'fechada' ? 'selected' : '' }}>Fechada</option>
                        </select>
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Descrição</label>
                        <textarea name="descricao" class="vm-input" rows="3">{{ $vaga->descricao }}</textarea>
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Requisitos</label>
                        <textarea name="requisitos" class="vm-input" rows="2">{{ $vaga->requisitos }}</textarea>
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Diferenciais</label>
                        <textarea name="diferenciais" class="vm-input" rows="2">{{ $vaga->diferenciais }}</textarea>
                    </div>
                    <div class="vm-form-full">
                        <label class="vm-label">Benefícios</label>
                        <textarea name="beneficios" class="vm-input" rows="2">{{ $vaga->beneficios }}</textarea>
                    </div>

                    <div class="vm-divider"></div>
                    <div class="vm-perguntas-wrap">
                        <div class="vm-perguntas-title"><i class="bi bi-chat-square-text-fill" style="color:#3b82f6"></i> Perguntas</div>
                        <div id="perguntasEditar{{ $vaga->id }}">
                            @foreach($vaga->perguntas as $pi => $p)
                            <div class="vm-pergunta-item">
                                <div class="vm-pergunta-row">
                                    <div>
                                        <label class="vm-label">Pergunta</label>
                                        <input type="text" name="perguntas[{{ $pi }}][texto]" class="vm-input" value="{{ $p->pergunta }}">
                                    </div>
                                    <div>
                                        <label class="vm-label">Tipo</label>
                                        <select name="perguntas[{{ $pi }}][tipo]" class="vm-input" onchange="vagaToggleOpcoes(this)">
                                            <option value="texto" {{ $p->tipo==='texto'?'selected':'' }}>Texto curto</option>
                                            <option value="textarea" {{ $p->tipo==='textarea'?'selected':'' }}>Texto longo</option>
                                            <option value="select" {{ $p->tipo==='select'?'selected':'' }}>Opções</option>
                                        </select>
                                    </div>
                                    <button type="button" class="vm-pergunta-remove" onclick="this.closest('.vm-pergunta-item').remove()"><i class="bi bi-x"></i></button>
                                </div>
                                <div class="vm-pergunta-opcoes {{ $p->tipo==='select' ? 'show' : '' }}">
                                    <label class="vm-label">Opções (separe por vírgula)</label>
                                    <input type="text" name="perguntas[{{ $pi }}][opcoes]" class="vm-input" placeholder="Ex: Sim, Não, Talvez" value="{{ $p->opcoes ? implode(', ', $p->opcoes) : '' }}">
                                    <div class="vm-hint" style="font-size:11px;color:#64748b;margin-top:4px">Cada opção separada por vírgula aparecerá como alternativa para o candidato.</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="vg-btn vg-btn-dark" onclick="vagaAddPergunta('perguntasEditar{{ $vaga->id }}','edit{{ $vaga->id }}')" style="margin-top:6px"><i class="bi bi-plus"></i> Adicionar pergunta</button>
                    </div>
                </div>
                <div class="vm-footer" style="padding:16px 0 0;border-top:1px solid rgba(148,163,184,.12);margin-top:16px">
                    <button type="button" class="vg-btn vg-btn-dark" onclick="closeVagaEditModal({{ $vaga->id }})">Cancelar</button>
                    <button type="submit" class="vg-btn vg-btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    var vagaPerguntaCounters = {};

    function openVagaModal(modal) {
        modal.classList.add('is-open');
        document.body.classList.add('modal-open');
    }

    function closeVagaModal(modal) {
        modal.classList.remove('is-open');
        document.body.classList.remove('modal-open');
    }

    function openVagaCriarModal() {
        openVagaModal(document.getElementById('vagaCriarModal'));
    }

    function closeVagaCriarModal() {
        closeVagaModal(document.getElementById('vagaCriarModal'));
    }

    function openVagaEditModal(vagaId) {
        openVagaModal(document.getElementById('vagaEditModal' + vagaId));
    }

    function closeVagaEditModal(vagaId) {
        closeVagaModal(document.getElementById('vagaEditModal' + vagaId));
    }

    function vagaToggleOpcoes(selectEl) {
        var item = selectEl.closest('.vm-pergunta-item');
        var opcoesDiv = item.querySelector('.vm-pergunta-opcoes');
        if (opcoesDiv) {
            if (selectEl.value === 'select') {
                opcoesDiv.classList.add('show');
            } else {
                opcoesDiv.classList.remove('show');
            }
        }
    }

    function vagaAddPergunta(containerId, prefix) {
        if (!vagaPerguntaCounters[prefix]) vagaPerguntaCounters[prefix] = document.querySelectorAll('#' + containerId + ' .vm-pergunta-item').length;
        var i = vagaPerguntaCounters[prefix]++;
        var div = document.createElement('div');
        div.className = 'vm-pergunta-item';

        var row = document.createElement('div');
        row.className = 'vm-pergunta-row';

        var pergDiv = document.createElement('div');
        pergDiv.innerHTML = '<label class="vm-label">Pergunta</label><input type="text" name="perguntas[' + i + '][texto]" class="vm-input" placeholder="Ex: Tem experiência com...?">';

        var tipoDiv = document.createElement('div');
        var sel = document.createElement('select');
        sel.name = 'perguntas[' + i + '][tipo]';
        sel.className = 'vm-input';
        sel.setAttribute('onchange', 'vagaToggleOpcoes(this)');
        sel.innerHTML = '<option value="texto">Texto curto</option><option value="textarea">Texto longo</option><option value="select">Opções</option>';
        var tipoLabel = document.createElement('label');
        tipoLabel.className = 'vm-label';
        tipoLabel.textContent = 'Tipo';
        tipoDiv.appendChild(tipoLabel);
        tipoDiv.appendChild(sel);

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'vm-pergunta-remove';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.onclick = function() { div.remove(); };

        row.appendChild(pergDiv);
        row.appendChild(tipoDiv);
        row.appendChild(removeBtn);

        var opcoesDiv = document.createElement('div');
        opcoesDiv.className = 'vm-pergunta-opcoes';
        opcoesDiv.innerHTML = '<label class="vm-label">Opções (separe por vírgula)</label><input type="text" name="perguntas[' + i + '][opcoes]" class="vm-input" placeholder="Ex: Sim, Não, Talvez"><div class="vm-hint">Cada opção separada por vírgula aparecerá como alternativa para o candidato.</div>';

        div.appendChild(row);
        div.appendChild(opcoesDiv);
        document.getElementById(containerId).appendChild(div);
    }

    function vagaCopyLink(url) {
        navigator.clipboard.writeText(url).then(function() {
            var toast = document.createElement('div');
            toast.textContent = '\u2713 Link copiado!';
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#22c55e;color:#fff;padding:12px 20px;border-radius:12px;font-weight:700;font-size:14px;z-index:99999';
            document.body.appendChild(toast);
            setTimeout(function() { toast.remove(); }, 2500);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVagaCriarModal();
            document.querySelectorAll('.custom-modal.is-open').forEach(function(m) {
                closeVagaModal(m);
            });
        }
    });
</script>

<script>
    function vagaInitCharts() {
        if (typeof Chart === 'undefined') {
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js';
            s.onload = function() { vagaBuildCharts(); };
            document.head.appendChild(s);
        } else {
            vagaBuildCharts();
        }
    }

    function vagaBuildCharts() {
        Chart.defaults.color = '#64748b';
        Chart.defaults.borderColor = 'rgba(148,163,184,0.06)';
        Chart.defaults.font.family = 'Inter, sans-serif';

        // Destroy any existing chart instances on these canvases
        ['chartDias', 'chartStatus', 'chartTopVagas'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el._chartInstance) { el._chartInstance.destroy(); }
        });

        // ── Line: Candidaturas últimos 14 dias ──
        var diasCtx = document.getElementById('chartDias');
        if (diasCtx) {
            diasCtx._chartInstance = new Chart(diasCtx, {
                type: 'line',
                data: {
                    labels: @json($chartDias->pluck('label')),
                    datasets: [{
                        label: 'Candidaturas',
                        data: @json($chartDias->pluck('total')),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#0f172a',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, min: 0, suggestedMax: 5, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.06)' } },
                        x: { ticks: { font: { size: 10 } }, grid: { color: 'rgba(148,163,184,0.03)' } }
                    }
                }
            });
        }

        // ── Doughnut: Status ──
        var statusCtx = document.getElementById('chartStatus');
        if (statusCtx) {
            var sd = @json($statusDist);
            var allKeys = ['nova', 'analisando', 'aprovada', 'reprovada'];
            var nameMap = { nova: 'Nova', analisando: 'Analisando', aprovada: 'Aprovada', reprovada: 'Reprovada' };
            var colorMap = { nova: '#38bdf8', analisando: '#f59e0b', aprovada: '#22c55e', reprovada: '#ef4444' };
            var statusLabels = [];
            var statusData = [];
            var statusColors = [];
            var hasData = false;
            for (var ki = 0; ki < allKeys.length; ki++) {
                var k = allKeys[ki];
                statusLabels.push(nameMap[k]);
                statusData.push(sd[k] || 0);
                statusColors.push(colorMap[k]);
                if (sd[k] > 0) hasData = true;
            }
            if (!hasData) {
                statusLabels = ['Sem candidatos'];
                statusData = [1];
                statusColors = ['rgba(148,163,184,0.08)'];
            }
            statusCtx._chartInstance = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusColors,
                        borderColor: '#0f172a',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'right', labels: { padding: 14, usePointStyle: true, pointStyle: 'circle', font: { size: 12 } } }
                    }
                }
            });
        }

        // ── Bar: Top vagas ──
        var topCtx = document.getElementById('chartTopVagas');
        if (topCtx) {
            var tv = @json($topVagas->values());
            topCtx._chartInstance = new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: tv.map(function(v) { return v.titulo; }),
                    datasets: [{
                        label: 'Candidatos',
                        data: tv.map(function(v) { return v.total; }),
                        backgroundColor: ['rgba(59,130,246,0.7)', 'rgba(96,165,250,0.6)', 'rgba(147,197,253,0.5)', 'rgba(191,219,254,0.4)', 'rgba(219,234,254,0.3)'],
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 28
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, min: 0, suggestedMax: 5, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.06)' } },
                        y: { ticks: { font: { size: 11 } }, grid: { display: false } }
                    }
                }
            });
        }
    }


    function vagaProcessarImagem(input) {
        if (!input.files || !input.files[0]) return;
        
        const btn = document.getElementById('btnImportarIA');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Analisando imagem...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('imagem', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route('vagas.analisar-imagem') }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Erro: ' + data.error);
            } else {
                const modal = document.getElementById('vagaCriarModal');
                if (data.titulo) modal.querySelector('[name="titulo"]').value = data.titulo;
                if (data.descricao) modal.querySelector('[name="descricao"]').value = data.descricao;
                if (data.requisitos) modal.querySelector('[name="requisitos"]').value = data.requisitos;
                if (data.diferenciais) modal.querySelector('[name="diferenciais"]').value = data.diferenciais;
                if (data.beneficios) modal.querySelector('[name="beneficios"]').value = data.beneficios;
                if (data.local) modal.querySelector('[name="local"]').value = data.local;
                if (data.salario) modal.querySelector('[name="salario"]').value = data.salario;
                
                alert('Dados importados com sucesso! Revise os campos antes de criar.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erro ao processar imagem.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            input.value = '';
        });
    }

    vagaInitCharts();
</script>

@endsection

