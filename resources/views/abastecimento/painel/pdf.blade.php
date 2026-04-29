<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Combustível</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 16px 18px 18px 18px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #0f172a;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .header {
            width: 100%;
            margin-bottom: 12px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-box {
            width: 110px;
        }

        .logo {
            max-width: 95px;
            max-height: 60px;
        }

        .title-box {
            text-align: right;
        }

        .title-box h1 {
            margin: 0;
            font-size: 21px;
            line-height: 1.1;
            color: #0f172a;
        }

        .title-box .subtitle {
            margin-top: 4px;
            font-size: 11px;
            color: #475569;
        }

        .title-box .period {
            margin-top: 3px;
            font-size: 10px;
            color: #64748b;
        }

        .top-line {
            height: 4px;
            background: #2563eb;
            border-radius: 999px;
            margin: 10px 0 14px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .meta-table td {
            width: 25%;
            border: 1px solid #dbe2ea;
            padding: 10px 12px;
            vertical-align: top;
            background: #f8fafc;
        }

        .meta-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .meta-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.15;
        }

        .meta-subvalue {
            margin-top: 4px;
            font-size: 9px;
            color: #475569;
        }

        .section-title {
            margin: 14px 0 8px;
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .summary-table td {
            width: 16.66%;
            border: 1px solid #dbe2ea;
            padding: 8px 10px;
            vertical-align: top;
            background: #ffffff;
        }

        .summary-label {
            font-size: 8.5px;
            color: #64748b;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }

        table.relatorio {
            width: 100%;
            border-collapse: collapse;
        }

        table.relatorio thead th {
            background: #2563eb;
            color: #ffffff;
            border: 1px solid #1d4ed8;
            padding: 7px 6px;
            text-align: left;
            font-size: 9px;
        }

        table.relatorio tbody td {
            border: 1px solid #dbe2ea;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            font-size: 9px;
            line-height: 1.35;
        }

        table.relatorio tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        .status {
            font-weight: bold;
        }

        .status.pendente {
            color: #b45309;
        }

        .status.aprovada {
            color: #047857;
        }

        .status.reprovada {
            color: #b91c1c;
        }

        .status.ajustada {
            color: #1d4ed8;
        }

        .muted {
            color: #64748b;
        }

        .footer {
            margin-top: 10px;
            border-top: 1px solid #dbe2ea;
            padding-top: 6px;
            font-size: 8.5px;
            color: #64748b;
            text-align: right;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('logo.png');

        $pendentes = (int) ($cards['pendentes'] ?? 0);
        $aprovadas = (int) ($cards['aprovadas'] ?? 0);
        $reprovadas = (int) ($cards['reprovadas'] ?? 0);
        $ajustadas = (int) ($cards['ajustadas'] ?? 0);
        $total = (int) ($cards['total'] ?? 0);
        $valorAprovado = (float) ($cards['valor_aprovado'] ?? 0);
        $litrosAprovados = (float) ($cards['litros_aprovados'] ?? 0);

        $taxaAprovacao = $total > 0 ? (($aprovadas + $ajustadas) / $total) * 100 : 0;
        $taxaReprovacao = $total > 0 ? ($reprovadas / $total) * 100 : 0;

        $usuariosUnicos = $solicitacoes
            ->filter(fn ($item) => $item->usuario)
            ->groupBy(fn ($item) => $item->usuario->id)
            ->count();

        $veiculosUnicos = $solicitacoes
            ->filter(fn ($item) => $item->veiculo)
            ->groupBy(fn ($item) => $item->veiculo->id)
            ->count();

        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        $periodo = isset($mes) && $mes
            ? ($meses[(int) $mes] ?? 'Mês') . ' / ' . $ano
            : 'Ano de ' . $ano;
    @endphp

    <div class="page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-box">
                        @if (file_exists($logoPath))
                            <img src="{{ $logoPath }}" class="logo" alt="Logo da empresa">
                        @endif
                    </td>
                    <td class="title-box">
                        <h1>Relatório de Combustível</h1>
                        <div class="subtitle">Painel gerencial do módulo de abastecimento</div>
                        <div class="period">Período analisado: {{ $periodo }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="top-line"></div>

        <table class="meta-table">
            <tr>
                <td>
                    <div class="meta-label">Período</div>
                    <div class="meta-value">{{ $periodo }}</div>
                    <div class="meta-subvalue">Filtro aplicado no relatório</div>
                </td>
                <td>
                    <div class="meta-label">Total de solicitações</div>
                    <div class="meta-value">{{ $total }}</div>
                    <div class="meta-subvalue">{{ $usuariosUnicos }} usuário(s) • {{ $veiculosUnicos }} veículo(s)</div>
                </td>
                <td>
                    <div class="meta-label">Valor aprovado</div>
                    <div class="meta-value">R$ {{ number_format($valorAprovado, 2, ',', '.') }}</div>
                    <div class="meta-subvalue">Solicitações do tipo valor</div>
                </td>
                <td>
                    <div class="meta-label">Emitido em</div>
                    <div class="meta-value">{{ now()->format('d/m/Y H:i') }}</div>
                    <div class="meta-subvalue">Relatório gerado automaticamente</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Indicadores do período</div>

        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Pendentes</div>
                    <div class="summary-value">{{ $pendentes }}</div>
                </td>
                <td>
                    <div class="summary-label">Aprovadas</div>
                    <div class="summary-value">{{ $aprovadas }}</div>
                </td>
                <td>
                    <div class="summary-label">Reprovadas</div>
                    <div class="summary-value">{{ $reprovadas }}</div>
                </td>
                <td>
                    <div class="summary-label">Ajustadas</div>
                    <div class="summary-value">{{ $ajustadas }}</div>
                </td>
                <td>
                    <div class="summary-label">Litros aprovados</div>
                    <div class="summary-value">{{ number_format($litrosAprovados, 2, ',', '.') }} L</div>
                </td>
                <td>
                    <div class="summary-label">Taxa de aprovação</div>
                    <div class="summary-value">{{ number_format($taxaAprovacao, 1, ',', '.') }}%</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Detalhamento das solicitações</div>

        <table class="relatorio">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Veículo</th>
                    <th>Data</th>
                    <th>KM</th>
                    <th>Tipo</th>
                    <th>Solicitado</th>
                    <th>Aprovado</th>
                    <th>Status</th>
                    <th>Obs. usuário</th>
                    <th>Obs. admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($solicitacoes as $solicitacao)
                    <tr>
                        <td>
                            {{ $solicitacao->usuario->name ?? '—' }}
                            <div class="muted">{{ $solicitacao->usuario->email ?? '—' }}</div>
                        </td>
                        <td>
                            @if ($solicitacao->veiculo)
                                {{ $solicitacao->veiculo->placa }} - {{ $solicitacao->veiculo->marca }} {{ $solicitacao->veiculo->modelo }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="nowrap">{{ optional($solicitacao->data_solicitacao)->format('d/m/Y') }}</td>
                        <td class="nowrap">{{ number_format((float) $solicitacao->km_informado, 1, ',', '.') }}</td>
                        <td class="nowrap">{{ strtoupper($solicitacao->tipo_solicitacao) }}</td>
                        <td class="nowrap">{{ number_format((float) $solicitacao->quantidade_solicitada, 2, ',', '.') }}</td>
                        <td class="nowrap">
                            {{ $solicitacao->quantidade_aprovada !== null
                                ? number_format((float) $solicitacao->quantidade_aprovada, 2, ',', '.')
                                : '—' }}
                        </td>
                        <td class="nowrap">
                            <span class="status {{ $solicitacao->status }}">
                                {{ strtoupper($solicitacao->status) }}
                            </span>
                        </td>
                        <td>{{ $solicitacao->observacao_usuario ?: '—' }}</td>
                        <td>{{ $solicitacao->observacao_admin ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Nenhuma solicitação encontrada para o período selecionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Sistema de Abastecimento • Relatório gerado automaticamente • Taxa de reprovação: {{ number_format($taxaReprovacao, 1, ',', '.') }}%
        </div>
    </div>
</body>
</html>