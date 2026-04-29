<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de estoque por obra</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px 0; }
        p { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table th, table td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        table th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Relatório de estoque por obra</h1>
    <p>Período: {{ $dataInicio->format('d/m/Y') }} até {{ $dataFim->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Obra</th>
                <th>Produto</th>
                <th>Variação</th>
                <th>Estoque atual</th>
                <th>Total entregue</th>
                <th>Média diária</th>
                <th>Dias restantes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estoques as $item)
                <tr>
                    <td>{{ $item['obra_nome'] }}</td>
                    <td>{{ $item['produto_nome'] }}</td>
                    <td>{{ $item['variacao_nome'] ?? '-' }}</td>
                    <td>{{ number_format((float) $item['estoque_atual'], 0, ',', '.') }}</td>
                    <td>{{ number_format((float) $item['total_entregue'], 0, ',', '.') }}</td>
                    <td>{{ number_format((float) $item['media_diaria'], 2, ',', '.') }}</td>
                    <td>
                        @if($item['dias_restantes'] === null)
                            Sem consumo
                        @else
                            {{ number_format($item['dias_restantes'], 1, ',', '.') }} dias
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhum dado encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>