<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de comprovantes pendentes</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table th, table td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        table th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Relatório de comprovantes pendentes</h1>

    <table>
        <thead>
            <tr>
                <th>Entrega</th>
                <th>Data</th>
                <th>Funcionário</th>
                <th>Obra</th>
                <th>Registrado por</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dados as $item)
                <tr>
                    <td>#{{ $item->id }}</td>
                    <td>{{ $item->data_entrega?->format('d/m/Y') }}</td>
                    <td>{{ $item->funcionario->nome ?? '-' }}</td>
                    <td>{{ $item->obra->nome ?? '-' }}</td>
                    <td>{{ $item->usuario->name ?? 'Sistema' }}</td>
                    <td>Pendente</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum comprovante pendente.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>