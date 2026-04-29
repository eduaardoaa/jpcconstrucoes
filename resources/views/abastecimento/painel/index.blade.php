@extends('layouts.app')

@section('title', 'Painel de Combustível')
@section('pageTitle', 'Painel de Combustível')
@section('pageDescription', 'Acompanhe métricas, gráficos e relatórios do módulo de abastecimento.')

@section('content')
    @php
        $totalSolicitacoes = (int) ($cards['total'] ?? 0);
        $pendentes = (int) ($cards['pendentes'] ?? 0);
        $aprovadas = (int) ($cards['aprovadas'] ?? 0);
        $reprovadas = (int) ($cards['reprovadas'] ?? 0);
        $ajustadas = (int) ($cards['ajustadas'] ?? 0);
        $valorAprovado = (float) ($cards['valor_aprovado'] ?? 0);
        $litrosAprovados = (float) ($cards['litros_aprovados'] ?? 0);

        $concluidas = $aprovadas + $ajustadas + $reprovadas;
        $taxaAprovacao = $totalSolicitacoes > 0 ? (($aprovadas + $ajustadas) / $totalSolicitacoes) * 100 : 0;
        $taxaReprovacao = $totalSolicitacoes > 0 ? ($reprovadas / $totalSolicitacoes) * 100 : 0;

        $veiculosUnicos = $solicitacoes
            ->filter(fn ($item) => $item->veiculo)
            ->groupBy(fn ($item) => $item->veiculo->id)
            ->count();

        $usuariosUnicos = $solicitacoes
            ->filter(fn ($item) => $item->usuario)
            ->groupBy(fn ($item) => $item->usuario->id)
            ->count();

        $tiposAgrupados = $solicitacoes
            ->groupBy('tipo_solicitacao')
            ->map(fn ($grupo) => $grupo->count());

        $tipoChart = [
            'labels' => ['Valor', 'Litros'],
            'values' => [
                (int) ($tiposAgrupados['valor'] ?? 0),
                (int) ($tiposAgrupados['litros'] ?? 0),
            ],
        ];

        $topUsuariosAgrupados = $solicitacoes
            ->filter(fn ($item) => $item->usuario)
            ->groupBy(fn ($item) => $item->usuario->name ?? 'Usuário')
            ->map(fn ($grupo) => $grupo->count())
            ->sortDesc()
            ->take(7);

        $usuarioChart = [
            'labels' => $topUsuariosAgrupados->keys()->values()->toArray(),
            'values' => $topUsuariosAgrupados->values()->toArray(),
        ];

        $labelsBase = $mesChart['labels'] ?? [];
        $statusPeriodo = [
            'pendente' => array_fill(0, count($labelsBase), 0),
            'aprovada' => array_fill(0, count($labelsBase), 0),
            'reprovada' => array_fill(0, count($labelsBase), 0),
            'ajustada' => array_fill(0, count($labelsBase), 0),
        ];

        if ($mes) {
            foreach ($solicitacoes as $item) {
                $dia = optional($item->data_solicitacao)->day;
                if ($dia && isset($statusPeriodo[$item->status])) {
                    $statusPeriodo[$item->status][$dia - 1]++;
                }
            }
        } else {
            foreach ($solicitacoes as $item) {
                $mesNumero = optional($item->data_solicitacao)->month;
                if ($mesNumero && isset($statusPeriodo[$item->status])) {
                    $statusPeriodo[$item->status][$mesNumero - 1]++;
                }
            }
        }

        $tituloPeriodo = $mes ? (($meses[$mes] ?? 'Mês') . ' de ' . $ano) : ('Ano de ' . $ano);
    @endphp

    <style>
        :root {
            --accent: #2563eb;
            --accent-2: #1d4ed8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --muted: #94a3b8;
            --text: #e2e8f0;
            --text-strong: #f8fafc;
            --border: rgba(148, 163, 184, .14);
            --card-bg: linear-gradient(180deg, rgba(15, 23, 42, .98), rgba(10, 15, 28, .98));
        }

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

        .page-head h2 {
            color: var(--text-strong);
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .page-head p {
            margin-top: 4px;
            color: var(--muted);
        }

        .actions-inline {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-dark-primary,
        .btn-secondary-dark {
            border: none;
            border-radius: 14px;
            padding: 10px 16px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: .2s ease;
        }

        .btn-dark-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 10px 24px rgba(37, 99, 235, .22);
        }

        .btn-dark-primary:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-secondary-dark {
            background: rgba(51, 65, 85, .95);
        }

        .btn-secondary-dark:hover {
            color: #fff;
            background: rgba(71, 85, 105, 1);
        }

        .filter-card,
        .stats-card,
        .chart-card,
        .table-card,
        .highlight-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, .22);
        }

        .card-head {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(148, 163, 184, .12);
        }

        .card-head h3 {
            margin: 0;
            color: var(--text-strong);
            font-size: 1.02rem;
            font-weight: 700;
        }

        .card-head p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: .9rem;
        }

        .card-body {
            padding: 20px;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 180px;
        }

        .form-label {
            color: #cbd5e1;
            font-size: .85rem;
            font-weight: 600;
        }

        .form-control-custom {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, .16);
            background: rgba(15, 23, 42, .95);
            color: #f8fafc;
            padding: 12px 14px;
            outline: none;
            box-shadow: none;
        }

        .highlight-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr 1fr;
            gap: 16px;
            margin: 18px 0;
        }

        .highlight-card {
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .highlight-card::after {
            content: "";
            position: absolute;
            right: -30px;
            bottom: -30px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(37, 99, 235, .18), transparent 70%);
            pointer-events: none;
        }

        .highlight-main {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .highlight-tag {
            display: inline-flex;
            width: fit-content;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(37, 99, 235, .14);
            color: #bfdbfe;
            font-size: .78rem;
            font-weight: 700;
            border: 1px solid rgba(96, 165, 250, .16);
        }

        .highlight-title {
            color: var(--text-strong);
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.15;
        }

        .highlight-subtitle {
            color: var(--muted);
            font-size: .92rem;
        }

        .highlight-big-number {
            color: var(--text-strong);
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-top: 6px;
        }

        .mini-kpi-label {
            color: var(--muted);
            font-size: .8rem;
            margin-bottom: 8px;
            display: block;
        }

        .mini-kpi-value {
            color: var(--text-strong);
            font-size: 1.55rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .mini-kpi-meta {
            margin-top: 10px;
            color: var(--muted);
            font-size: .82rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin: 18px 0;
        }

        .stats-card {
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .stats-card::after {
            content: "";
            position: absolute;
            right: -18px;
            bottom: -18px;
            width: 70px;
            height: 70px;
            background: radial-gradient(circle, rgba(37, 99, 235, .16), transparent 70%);
            pointer-events: none;
        }

        .stats-label {
            color: var(--muted);
            font-size: .82rem;
            margin-bottom: 8px;
            display: block;
        }

        .stats-value {
            color: var(--text-strong);
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1.15;
        }

        .stats-meta {
            margin-top: 10px;
            color: var(--muted);
            font-size: .8rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            margin-bottom: 18px;
        }

        .chart-card.full {
            grid-column: 1 / -1;
        }

        .chart-wrap {
            padding: 20px;
            height: 360px;
        }

        .chart-wrap.tall {
            height: 420px;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .table-dark-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-dark-custom thead th {
            color: var(--muted);
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

        .muted-line {
            color: var(--muted);
            font-size: .82rem;
            display: block;
            margin-top: 4px;
        }

        @media (max-width: 1200px) {
            .highlight-grid {
                grid-template-columns: 1fr 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1100px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-card.full {
                grid-column: auto;
            }
        }

        @media (max-width: 768px) {
            .highlight-grid,
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group {
                min-width: unset;
            }

            .actions-inline {
                width: 100%;
            }

            .actions-inline a,
            .actions-inline button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="page-head">
        <div>
            <h2>Painel de combustível</h2>
            <p>Indicadores, gráficos e visão gerencial do abastecimento em {{ $tituloPeriodo }}.</p>
        </div>

        <div class="actions-inline">
            <a href="{{ route('abastecimento.painel.pdf', ['ano' => $ano, 'mes' => $mes]) }}" target="_blank" class="btn-dark-primary">
                <i class="bi bi-file-earmark-pdf-fill"></i> Baixar PDF
            </a>
        </div>
    </div>

    <div class="filter-card">
        <div class="card-head">
            <h3>Filtro</h3>
            <p>Selecione mês e ano para atualizar o painel completo.</p>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('abastecimento.painel.index') }}" class="filter-form">
                <div class="form-group">
                    <label class="form-label">Mês</label>
                    <select name="mes" class="form-control-custom">
                        <option value="">Todos</option>
                        @foreach ($meses as $numeroMes => $nomeMes)
                            <option value="{{ $numeroMes }}" {{ (int) $mes === (int) $numeroMes ? 'selected' : '' }}>
                                {{ $nomeMes }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ano</label>
                    <select name="ano" class="form-control-custom">
                        @foreach ($anos as $anoOption)
                            <option value="{{ $anoOption }}" {{ (int) $ano === (int) $anoOption ? 'selected' : '' }}>
                                {{ $anoOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="actions-inline">
                    <button type="submit" class="btn-dark-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>

                    <a href="{{ route('abastecimento.painel.index') }}" class="btn-secondary-dark">
                        <i class="bi bi-arrow-counterclockwise"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="highlight-grid">
        <div class="highlight-card">
            <div class="highlight-main">
                <span class="highlight-tag">Resumo do período</span>
                <div class="highlight-title">{{ $tituloPeriodo }}</div>
                <div class="highlight-subtitle">
                    {{ $totalSolicitacoes }} solicitação(ões), {{ $usuariosUnicos }} usuário(s) e {{ $veiculosUnicos }} veículo(s) envolvidos.
                </div>
                <div class="highlight-big-number">{{ number_format($taxaAprovacao, 1, ',', '.') }}%</div>
                <div class="highlight-subtitle">Taxa de aprovação do período.</div>
            </div>
        </div>

        <div class="highlight-card">
            <span class="mini-kpi-label">Valor aprovado</span>
            <div class="mini-kpi-value">R$ {{ number_format($valorAprovado, 2, ',', '.') }}</div>
            <div class="mini-kpi-meta">Somatório aprovado em solicitações por valor.</div>
        </div>

        <div class="highlight-card">
            <span class="mini-kpi-label">Litros aprovados</span>
            <div class="mini-kpi-value">{{ number_format($litrosAprovados, 2, ',', '.') }} L</div>
            <div class="mini-kpi-meta">Somatório aprovado em solicitações por litros.</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stats-card">
            <span class="stats-label">Total de solicitações</span>
            <div class="stats-value">{{ $totalSolicitacoes }}</div>
            <div class="stats-meta">Volume geral do período</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Pendentes</span>
            <div class="stats-value">{{ $pendentes }}</div>
            <div class="stats-meta">Aguardando análise</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Aprovadas</span>
            <div class="stats-value">{{ $aprovadas }}</div>
            <div class="stats-meta">Liberadas sem ajuste</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Ajustadas</span>
            <div class="stats-value">{{ $ajustadas }}</div>
            <div class="stats-meta">Liberadas com alteração</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Reprovadas</span>
            <div class="stats-value">{{ $reprovadas }}</div>
            <div class="stats-meta">Solicitações negadas</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Concluídas</span>
            <div class="stats-value">{{ $concluidas }}</div>
            <div class="stats-meta">Processadas no período</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Usuários únicos</span>
            <div class="stats-value">{{ $usuariosUnicos }}</div>
            <div class="stats-meta">Com solicitações registradas</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Veículos únicos</span>
            <div class="stats-value">{{ $veiculosUnicos }}</div>
            <div class="stats-meta">Com movimentação no período</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Taxa de aprovação</span>
            <div class="stats-value">{{ number_format($taxaAprovacao, 1, ',', '.') }}%</div>
            <div class="stats-meta">Aprovadas + ajustadas sobre o total</div>
        </div>

        <div class="stats-card">
            <span class="stats-label">Taxa de reprovação</span>
            <div class="stats-value">{{ number_format($taxaReprovacao, 1, ',', '.') }}%</div>
            <div class="stats-meta">Reprovadas sobre o total</div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <div class="card-head">
                <h3>Solicitações por status</h3>
                <p>Distribuição geral das solicitações no período.</p>
            </div>
            <div class="chart-wrap">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="card-head">
                <h3>{{ $mes ? 'Solicitações por dia' : 'Solicitações por mês' }}</h3>
                <p>{{ $mes ? 'Evolução diária dentro do mês selecionado.' : 'Evolução mensal ao longo do ano selecionado.' }}</p>
            </div>
            <div class="chart-wrap">
                <canvas id="mesChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="card-head">
                <h3>Distribuição por tipo</h3>
                <p>Comparativo entre solicitações por valor e por litros.</p>
            </div>
            <div class="chart-wrap">
                <canvas id="tipoChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="card-head">
                <h3>Status no período</h3>
                <p>{{ $mes ? 'Composição diária por situação.' : 'Composição mensal por situação.' }}</p>
            </div>
            <div class="chart-wrap">
                <canvas id="statusPeriodoChart"></canvas>
            </div>
        </div>

        <div class="chart-card full">
            <div class="card-head">
                <h3>Top veículos por quantidade de solicitações</h3>
                <p>Ranking de veículos com maior volume no período.</p>
            </div>
            <div class="chart-wrap tall">
                <canvas id="veiculoChart"></canvas>
            </div>
        </div>

        <div class="chart-card full">
            <div class="card-head">
                <h3>Top usuários por quantidade de solicitações</h3>
                <p>Usuários com maior recorrência de solicitações no período.</p>
            </div>
            <div class="chart-wrap tall">
                <canvas id="usuarioChart"></canvas>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="card-head">
            <h3>Últimas solicitações</h3>
            <p>Últimos 15 registros encontrados em {{ $tituloPeriodo }}.</p>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table class="table-dark-custom">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Veículo</th>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Solicitado</th>
                            <th>Aprovado</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($solicitacoes->take(15) as $solicitacao)
                            <tr>
                                <td>
                                    {{ $solicitacao->usuario->name ?? '—' }}
                                    <span class="muted-line">{{ $solicitacao->usuario->email ?? '—' }}</span>
                                </td>
                                <td>
                                    @if ($solicitacao->veiculo)
                                        {{ $solicitacao->veiculo->placa }} - {{ $solicitacao->veiculo->marca }} {{ $solicitacao->veiculo->modelo }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ optional($solicitacao->data_solicitacao)->format('d/m/Y') }}</td>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; color:#94a3b8;">
                                    Nenhuma solicitação encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.color = '#cbd5e1';
        Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.10)';
        Chart.defaults.font.family = 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';

        const statusCtx = document.getElementById('statusChart');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($statusChart['labels']),
                datasets: [{
                    data: @json($statusChart['values']),
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                        'rgba(59, 130, 246, 0.85)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });

        const mesCtx = document.getElementById('mesChart');
        new Chart(mesCtx, {
            type: 'line',
            data: {
                labels: @json($mesChart['labels']),
                datasets: [{
                    label: 'Solicitações',
                    data: @json($mesChart['values']),
                    borderColor: 'rgba(37, 99, 235, 1)',
                    backgroundColor: 'rgba(37, 99, 235, 0.18)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        const tipoCtx = document.getElementById('tipoChart');
        new Chart(tipoCtx, {
            type: 'pie',
            data: {
                labels: @json($tipoChart['labels']),
                datasets: [{
                    data: @json($tipoChart['values']),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.85)',
                        'rgba(16, 185, 129, 0.85)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const statusPeriodoCtx = document.getElementById('statusPeriodoChart');
        new Chart(statusPeriodoCtx, {
            type: 'bar',
            data: {
                labels: @json($mesChart['labels']),
                datasets: [
                    {
                        label: 'Pendentes',
                        data: @json($statusPeriodo['pendente']),
                        backgroundColor: 'rgba(245, 158, 11, 0.82)',
                        borderRadius: 6
                    },
                    {
                        label: 'Aprovadas',
                        data: @json($statusPeriodo['aprovada']),
                        backgroundColor: 'rgba(16, 185, 129, 0.82)',
                        borderRadius: 6
                    },
                    {
                        label: 'Reprovadas',
                        data: @json($statusPeriodo['reprovada']),
                        backgroundColor: 'rgba(239, 68, 68, 0.82)',
                        borderRadius: 6
                    },
                    {
                        label: 'Ajustadas',
                        data: @json($statusPeriodo['ajustada']),
                        backgroundColor: 'rgba(59, 130, 246, 0.82)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        beginAtZero: true,
                        stacked: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        const veiculoCtx = document.getElementById('veiculoChart');
        new Chart(veiculoCtx, {
            type: 'bar',
            data: {
                labels: @json($veiculoChart['labels']),
                datasets: [{
                    label: 'Solicitações',
                    data: @json($veiculoChart['values']),
                    backgroundColor: 'rgba(37, 99, 235, 0.82)',
                    borderRadius: 10
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        const usuarioCtx = document.getElementById('usuarioChart');
        new Chart(usuarioCtx, {
            type: 'bar',
            data: {
                labels: @json($usuarioChart['labels']),
                datasets: [{
                    label: 'Solicitações',
                    data: @json($usuarioChart['values']),
                    backgroundColor: 'rgba(16, 185, 129, 0.82)',
                    borderRadius: 10
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    </script>
@endsection