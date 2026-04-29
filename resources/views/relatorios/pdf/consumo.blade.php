<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de consumo por produto</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 10px 0;
        }

        p {
            margin: 0 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        table th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <h1>Relatório de consumo por produto</h1>
    <p>Produtos e variações agrupados pela quantidade total entregue.</p>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Variação</th>
                <th>Total consumido</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dados as $item)
                <tr>
                    <td>{{ $item['produto'] }}</td>
                    <td>{{ $item['variacao'] ?? '-' }}</td>
                    <td>{{ number_format((float) $item['total'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Nenhum consumo registrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>