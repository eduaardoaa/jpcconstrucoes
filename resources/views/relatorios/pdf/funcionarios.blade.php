
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de entregas por funcionário</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table th, table td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        table th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Relatório de entregas por funcionário</h1>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Matrícula</th>
                <th>Telefone</th>
                <th>Obra</th>
                <th>Cargo</th>
                <th>Status</th>
                <th>Admissão</th>
                <th>Total entregas</th>
                <th>Total itens</th>
                <th>Última entrega</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dados as $item)
                <tr>
                    <td>{{ $item['nome'] }}</td>
                    <td>{{ $item['cpf'] ?: '-' }}</td>
                    <td>{{ $item['matricula'] ?: '-' }}</td>
                    <td>{{ $item['telefone'] ?: '-' }}</td>
                    <td>{{ $item['obra'] }}</td>
                    <td>{{ $item['cargo'] }}</td>
                    <td>{{ ucfirst($item['status']) }}</td>
                    <td>{{ $item['data_admissao'] ? \Carbon\Carbon::parse($item['data_admissao'])->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item['total_entregas'] }}</td>
                    <td>{{ number_format((float) $item['total_itens'], 0, ',', '.') }}</td>
                    <td>{{ $item['ultima_entrega'] ? \Carbon\Carbon::parse($item['ultima_entrega'])->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">Nenhum funcionário encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>