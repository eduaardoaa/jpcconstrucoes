@extends('layouts.app')

@section('title', 'Controle de Deslocamento')
@section('pageTitle', 'Controle de Deslocamento')
@section('pageDescription', 'Acompanhe o histórico geral de deslocamentos dos veículos.')

@section('content')
    <style>
        /* ── Base ─────────────────────────────────────────────────────────────── */
        .page-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
        .page-head h2, .page-head p { margin:0; }

        .dark-card {
            background: linear-gradient(180deg, rgba(15,23,42,.98), rgba(10,15,28,.98));
            border: 1px solid rgba(148,163,184,.14);
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(0,0,0,.22);
            overflow: hidden;
            margin-bottom: 18px;
        }
        .dark-card-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(148,163,184,.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .dark-card-header h3 { margin:0; color:#f8fafc; font-size:1.02rem; font-weight:700; }
        .dark-card-header p  { margin:4px 0 0; color:#94a3b8; font-size:.9rem; }
        .dark-card-body { padding:20px; }

        /* ── Filtros ──────────────────────────────────────────────────────────── */
        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        .filters-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .form-group { display:flex; flex-direction:column; gap:7px; }
        .form-label { color:#94a3b8; font-size:.78rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }

        .form-control-custom,
        .form-select-custom {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(148,163,184,.18);
            background: rgba(2,6,23,.55);
            color: #f8fafc;
            padding: 11px 14px;
            outline: none;
            font-size: .92rem;
        }
        .form-select-custom option { background:#0f172a; }
        .form-control-custom::-webkit-calendar-picker-indicator { filter: invert(1) opacity(.5); }

        .filter-divider {
            border: none;
            border-top: 1px solid rgba(148,163,184,.10);
            margin: 12px 0;
        }

        /* ── Botões ───────────────────────────────────────────────────────────── */
        .btn-dark-primary {
            border: none; border-radius: 12px; padding: 11px 18px;
            font-weight: 600; color: #fff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 8px 20px rgba(37,99,235,.25);
            white-space: nowrap; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-dark-primary:hover { transform:translateY(-1px); color:#fff; }

        .btn-secondary-dark {
            border: 1px solid rgba(148,163,184,.2); border-radius: 12px; padding: 11px 18px;
            font-weight: 600; color: #cbd5e1;
            background: rgba(15,23,42,.7);
            white-space: nowrap; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-secondary-dark:hover { color:#fff; background:rgba(30,41,59,.9); }

        .filter-actions { display:flex; gap:10px; flex-wrap:wrap; }

        /* ── Chips de filtros ativos ──────────────────────────────────────────── */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(37,99,235,.15);
            border: 1px solid rgba(96,165,250,.2);
            border-radius: 999px;
            padding: 4px 12px;
            font-size: .8rem;
            color: #93c5fd;
        }
        .filter-chip a {
            color: #64748b;
            text-decoration: none;
            line-height: 1;
        }
        .filter-chip a:hover { color: #f87171; }

        /* ── Badge ────────────────────────────────────────────────────────────── */
        .badge-status {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 11px; border-radius: 999px;
            font-size: .78rem; font-weight: 700; white-space: nowrap;
        }
        .badge-status.em_andamento { background:rgba(245,158,11,.14); color:#fbbf24; border:1px solid rgba(251,191,36,.16); }
        .badge-status.finalizado   { background:rgba(16,185,129,.15); color:#34d399; border:1px solid rgba(52,211,153,.2); }

        /* ── GPS link ─────────────────────────────────────────────────────────── */
        .gps-maps-link { color:#93c5fd; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-size:.83rem; }
        .gps-maps-link:hover { color:#bfdbfe; text-decoration:underline; }

        /* ── Foto ─────────────────────────────────────────────────────────────── */
        .thumb-foto { width:56px; height:56px; object-fit:cover; border-radius:10px; border:1px solid rgba(148,163,184,.16); display:block; margin-top:6px; }

        /* ── Empty ────────────────────────────────────────────────────────────── */
        .empty-state { padding:40px 20px; text-align:center; color:#94a3b8; }
        .empty-state i { font-size:2.2rem; display:block; margin-bottom:10px; color:#334155; }

        /* ══ DESKTOP — tabela ═══════════════════════════════════════════════════ */
        .table-wrap { width:100%; overflow-x:auto; }

        .table-dark-custom {
            width:100%; margin:0;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .table-dark-custom thead th {
            color:#64748b; font-size:.75rem; font-weight:700;
            letter-spacing:.06em; text-transform:uppercase;
            border:none; padding:0 14px 6px; white-space:nowrap;
        }
        .table-dark-custom tbody tr { background:rgba(15,23,42,.86); }
        .table-dark-custom tbody td {
            color:#e2e8f0; border:none; padding:16px 14px; vertical-align:top;
            border-top:1px solid rgba(148,163,184,.07);
            border-bottom:1px solid rgba(148,163,184,.07);
        }
        .table-dark-custom tbody tr td:first-child { border-left:1px solid rgba(148,163,184,.07); border-radius:14px 0 0 14px; }
        .table-dark-custom tbody tr td:last-child  { border-right:1px solid rgba(148,163,184,.07); border-radius:0 14px 14px 0; }

        .user-name    { color:#f8fafc; font-weight:700; font-size:.95rem; }
        .user-sub     { color:#64748b; font-size:.82rem; margin-top:2px; }
        .veiculo-placa { color:#f8fafc; font-weight:700; font-size:.95rem; }
        .veiculo-sub   { color:#64748b; font-size:.82rem; margin-top:2px; }

        .etapas-list { display:flex; flex-direction:column; gap:8px; min-width:280px; }
        .etapa-item {
            padding:11px 13px; border-radius:12px;
            background:rgba(2,6,23,.4);
            border:1px solid rgba(148,163,184,.08);
        }
        .etapa-tipo  { font-size:.72rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#93c5fd; margin-bottom:4px; }
        .etapa-local { color:#f1f5f9; font-weight:600; font-size:.9rem; margin-bottom:5px; }
        .etapa-meta  { color:#64748b; font-size:.8rem; display:flex; flex-wrap:wrap; gap:6px 14px; margin-bottom:3px; }
        .etapa-obs   { color:#94a3b8; font-size:.8rem; margin-top:4px; font-style:italic; }

        /* ══ MOBILE — cards ═════════════════════════════════════════════════════ */
        .cards-mobile { display:none; }

        .desl-card {
            background:rgba(15,23,42,.92);
            border:1px solid rgba(148,163,184,.10);
            border-radius:18px; padding:16px; margin-bottom:12px;
        }
        .desl-card-top { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:12px; flex-wrap:wrap; }
        .desl-card-user { font-size:.95rem; font-weight:700; color:#f8fafc; }
        .desl-card-sub  { font-size:.82rem; color:#64748b; margin-top:2px; }

        .desl-card-info { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px; }
        .info-pill {
            background:rgba(30,41,59,.6);
            border:1px solid rgba(148,163,184,.10);
            border-radius:8px; padding:5px 10px;
            font-size:.8rem; color:#94a3b8;
        }
        .info-pill strong { color:#e2e8f0; }

        .desl-card-etapas { display:flex; flex-direction:column; gap:8px; }

        @media (max-width:860px) {
            .table-wrap { display:none; }
            .cards-mobile { display:block; }
            .filters-grid,
            .filters-grid-2 { grid-template-columns:1fr; }
            .filter-actions { width:100%; }
            .filter-actions .btn-dark-primary,
            .filter-actions .btn-secondary-dark { flex:1; justify-content:center; }
        }
        @media (max-width:480px) {
            .dark-card-body { padding:14px; }
            .desl-card { padding:14px; }
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
            <h2>Controle de deslocamento</h2>
            <p>Acompanhe o histórico geral de deslocamentos dos veículos.</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="dark-card">
        <div class="dark-card-header">
            <div>
                <h3>Filtros</h3>
                <p>Refine a visualização dos deslocamentos.</p>
            </div>
        </div>
        <div class="dark-card-body">
            <form method="GET" action="{{ route('deslocamentos.index') }}">

                {{-- Linha 1: Busca | Status | Usuário --}}
                <div class="filters-grid">
                    <div class="form-group">
                        <label class="form-label">Busca geral</label>
                        <input
                            type="text"
                            name="busca"
                            class="form-control-custom"
                            value="{{ $busca }}"
                            placeholder="Usuário, email, placa, marca, modelo..."
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select-custom">
                            <option value="">Todos</option>
                            <option value="em_andamento" {{ $status === 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                            <option value="finalizado"   {{ $status === 'finalizado'   ? 'selected' : '' }}>Finalizado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Usuário</label>
                        <select name="usuario_id" class="form-select-custom">
                            <option value="">Todos os usuários</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" {{ (string)$usuarioId === (string)$u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="filter-divider">

                {{-- Linha 2: Data início | Data fim | (espaço) | Ações --}}
                <div class="filters-grid-2">
                    <div class="form-group">
                        <label class="form-label">Data início</label>
                        <input
                            type="date"
                            name="data_inicio"
                            class="form-control-custom"
                            value="{{ $dataInicio }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Data fim</label>
                        <input
                            type="date"
                            name="data_fim"
                            class="form-control-custom"
                            value="{{ $dataFim }}"
                        >
                    </div>

                    <div></div>{{-- espaço --}}

                    <div class="filter-actions" style="justify-content:flex-end;">
                        <button type="submit" class="btn-dark-primary">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('deslocamentos.index') }}" class="btn-secondary-dark">
                            <i class="bi bi-x-lg"></i> Limpar
                        </a>
                    </div>
                </div>

                {{-- Chips dos filtros ativos --}}
                @php
                    $temFiltro = $busca || $status || $dataInicio || $dataFim || $usuarioId;
                @endphp

                @if ($temFiltro)
                    <div class="active-filters">
                        @if ($busca)
                            <span class="filter-chip">
                                <i class="bi bi-search"></i> "{{ $busca }}"
                                <a href="{{ request()->fullUrlWithoutQuery(['busca']) }}">×</a>
                            </span>
                        @endif
                        @if ($status)
                            <span class="filter-chip">
                                <i class="bi bi-circle-half"></i>
                                {{ $status === 'em_andamento' ? 'Em andamento' : 'Finalizado' }}
                                <a href="{{ request()->fullUrlWithoutQuery(['status']) }}">×</a>
                            </span>
                        @endif
                        @if ($usuarioId)
                            <span class="filter-chip">
                                <i class="bi bi-person"></i>
                                {{ $usuarios->firstWhere('id', $usuarioId)?->name ?? 'Usuário' }}
                                <a href="{{ request()->fullUrlWithoutQuery(['usuario_id']) }}">×</a>
                            </span>
                        @endif
                        @if ($dataInicio)
                            <span class="filter-chip">
                                <i class="bi bi-calendar-event"></i>
                                De {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }}
                                <a href="{{ request()->fullUrlWithoutQuery(['data_inicio']) }}">×</a>
                            </span>
                        @endif
                        @if ($dataFim)
                            <span class="filter-chip">
                                <i class="bi bi-calendar-event"></i>
                                Até {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
                                <a href="{{ request()->fullUrlWithoutQuery(['data_fim']) }}">×</a>
                            </span>
                        @endif
                    </div>
                @endif

            </form>
        </div>
    </div>

    {{-- ── HISTÓRICO ── --}}
    <div class="dark-card">
        <div class="dark-card-header">
            <div>
                <h3>Histórico geral</h3>
                <p>{{ $deslocamentos->count() }} deslocamento(s) encontrado(s).</p>
            </div>
        </div>
        <div class="dark-card-body">

            @if ($deslocamentos->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-map"></i>
                    Nenhum deslocamento encontrado para os filtros selecionados.
                </div>

            @else

                {{-- ══ DESKTOP — tabela ══ --}}
                <div class="table-wrap">
                    <table class="table-dark-custom">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Veículo</th>
                                <th>Motivo</th>
                                <th>Status</th>
                                <th>Etapas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deslocamentos as $deslocamento)
                                <tr>
                                    <td>
                                        <div class="user-name">{{ $deslocamento->usuario->name ?? '—' }}</div>
                                        <div class="user-sub">
                                            {{ $deslocamento->usuario->cargo->nome ?? ($deslocamento->usuario->email ?? '—') }}
                                        </div>
                                    </td>

                                    <td>
                                        @if ($deslocamento->veiculo)
                                            <div class="veiculo-placa">{{ $deslocamento->veiculo->placa }}</div>
                                            <div class="veiculo-sub">{{ $deslocamento->veiculo->marca }} {{ $deslocamento->veiculo->modelo }}</div>
                                        @else
                                            <span style="color:#475569">—</span>
                                        @endif
                                    </td>

                                    <td style="color:#94a3b8; font-size:.9rem;">
                                        {{ $deslocamento->motivo ?: '—' }}
                                    </td>

                                    <td>
                                        <span class="badge-status {{ $deslocamento->status }}">
                                            {{ strtoupper(str_replace('_', ' ', $deslocamento->status)) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="etapas-list">
                                            @foreach ($deslocamento->etapas as $etapa)
                                                <div class="etapa-item">
                                                    <div class="etapa-tipo">{{ $etapa->tipo_etapa }}</div>
                                                    <div class="etapa-local">{{ $etapa->local_etapa }}</div>
                                                    <div class="etapa-meta">
                                                        <span><i class="bi bi-calendar3"></i> {{ optional($etapa->data_etapa)->format('d/m/Y') }}</span>
                                                        <span><i class="bi bi-clock"></i> {{ $etapa->hora_etapa }}</span>
                                                        <span><i class="bi bi-speedometer2"></i> {{ number_format((float) $etapa->km_etapa, 1, ',', '.') }} km</span>
                                                    </div>

                                                    @if(!is_null($etapa->latitude) && !is_null($etapa->longitude))
                                                        <div>
                                                            <a class="gps-maps-link"
                                                               href="https://www.google.com/maps?q={{ $etapa->latitude }},{{ $etapa->longitude }}"
                                                               target="_blank" rel="noopener"
                                                               data-lat="{{ $etapa->latitude }}"
                                                               data-lng="{{ $etapa->longitude }}">
                                                                <i class="bi bi-geo-alt-fill"></i>
                                                                <span class="gps-endereco-texto">Carregando endereço...</span>
                                                            </a>
                                                        </div>
                                                    @endif

                                                    @if($etapa->observacao)
                                                        <div class="etapa-obs">{{ $etapa->observacao }}</div>
                                                    @endif

                                                    @if ($etapa->foto_painel)
                                                        <a href="{{ $fotoUrl($etapa->foto_painel) }}" target="_blank">
    <img src="{{ $fotoUrl($etapa->foto_painel) }}" alt="Foto painel" class="thumb-foto">
</a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ══ MOBILE — cards ══ --}}
                <div class="cards-mobile">
                    @foreach ($deslocamentos as $deslocamento)
                        <div class="desl-card">
                            <div class="desl-card-top">
                                <div>
                                    <div class="desl-card-user">{{ $deslocamento->usuario->name ?? '—' }}</div>
                                    <div class="desl-card-sub">
                                        {{ $deslocamento->usuario->cargo->nome ?? ($deslocamento->usuario->email ?? '—') }}
                                    </div>
                                </div>
                                <span class="badge-status {{ $deslocamento->status }}">
                                    {{ strtoupper(str_replace('_', ' ', $deslocamento->status)) }}
                                </span>
                            </div>

                            <div class="desl-card-info">
                                @if ($deslocamento->veiculo)
                                    <div class="info-pill">
                                        <i class="bi bi-truck"></i>
                                        <strong>{{ $deslocamento->veiculo->placa }}</strong>
                                        {{ $deslocamento->veiculo->marca }} {{ $deslocamento->veiculo->modelo }}
                                    </div>
                                @endif
                                @if ($deslocamento->motivo)
                                    <div class="info-pill">
                                        <i class="bi bi-tag"></i> {{ $deslocamento->motivo }}
                                    </div>
                                @endif
                            </div>

                            <div class="desl-card-etapas">
                                @foreach ($deslocamento->etapas as $etapa)
                                    <div class="etapa-item">
                                        <div class="etapa-tipo">{{ $etapa->tipo_etapa }}</div>
                                        <div class="etapa-local">{{ $etapa->local_etapa }}</div>
                                        <div class="etapa-meta">
                                            <span><i class="bi bi-calendar3"></i> {{ optional($etapa->data_etapa)->format('d/m/Y') }}</span>
                                            <span><i class="bi bi-clock"></i> {{ $etapa->hora_etapa }}</span>
                                            <span><i class="bi bi-speedometer2"></i> {{ number_format((float) $etapa->km_etapa, 1, ',', '.') }} km</span>
                                        </div>

                                        @if(!is_null($etapa->latitude) && !is_null($etapa->longitude))
                                            <div>
                                                <a class="gps-maps-link"
                                                   href="https://www.google.com/maps?q={{ $etapa->latitude }},{{ $etapa->longitude }}"
                                                   target="_blank" rel="noopener"
                                                   data-lat="{{ $etapa->latitude }}"
                                                   data-lng="{{ $etapa->longitude }}">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                    <span class="gps-endereco-texto">Carregando endereço...</span>
                                                </a>
                                            </div>
                                        @endif

                                        @if($etapa->observacao)
                                            <div class="etapa-obs">{{ $etapa->observacao }}</div>
                                        @endif

                                        @if ($etapa->foto_painel)
                                            <a href="{{ $fotoUrl($etapa->foto_painel) }}" target="_blank">
    <img src="{{ $fotoUrl($etapa->foto_painel) }}" alt="Foto painel" class="thumb-foto">
</a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            @endif
        </div>
    </div>

    <script>
        async function geocodificarReverso(lat, lng) {
            try {
                const resp = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&addressdetails=1`,
                    { headers: { 'Accept': 'application/json' } }
                );
                if (!resp.ok) throw new Error();
                const data = await resp.json();
                const a = data.address || {};

                const partes = [];
                if (a.road) partes.push(a.house_number ? `${a.road}, ${a.house_number}` : a.road);
                const bairro = a.suburb || a.neighbourhood || a.quarter;
                if (bairro) partes.push(bairro);
                const cidade = a.city || a.town || a.village || a.municipality;
                if (cidade) partes.push(cidade);
                if (a.postcode) partes.push(a.postcode);

                return partes.length ? partes.join(' – ') : `${lat}, ${lng}`;
            } catch {
                return `${lat}, ${lng}`;
            }
        }

        document.addEventListener('DOMContentLoaded', async function () {
            // Deduplica coordenadas — mesma coordenada = 1 única requisição
            const mapa = new Map();
            document.querySelectorAll('.gps-maps-link').forEach(link => {
                const key = `${link.dataset.lat},${link.dataset.lng}`;
                if (!mapa.has(key)) mapa.set(key, []);
                mapa.get(key).push(link.querySelector('.gps-endereco-texto'));
            });

            for (const [key, spans] of mapa) {
                const [lat, lng] = key.split(',');
                await new Promise(r => setTimeout(r, 350));
                const endereco = await geocodificarReverso(lat, lng);
                spans.forEach(span => { if (span) span.textContent = endereco; });
            }
        });
    </script>
@endsection