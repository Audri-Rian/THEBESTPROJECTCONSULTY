<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $metadata['report_name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .metadata {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .metadata p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .receita {
            color: #16a34a;
        }
        .despesa {
            color: #dc2626;
        }
        .totals {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $metadata['report_name'] }}</h1>
    </div>

    <div class="metadata">
        <p><strong>Gerado em:</strong> {{ $metadata['generated_at'] }}</p>
        <p><strong>Total de registros:</strong> {{ $metadata['total_records'] }}</p>
    </div>

    <div class="totals">
        <p><strong>Total de Receitas:</strong> R$ {{ number_format($metadata['total_receitas'], 2, ',', '.') }}</p>
        <p><strong>Total de Despesas:</strong> R$ {{ number_format($metadata['total_despesas'], 2, ',', '.') }}</p>
        <p><strong>Saldo:</strong> R$ {{ number_format($metadata['saldo'], 2, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Data</th>
                <th>Categoria/Tipo</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item['id'] }}</td>
                <td>{{ $item['nome'] }}</td>
                <td>{{ $item['descricao'] }}</td>
                <td class="{{ $item['tipo'] === 'Receita' ? 'receita' : 'despesa' }}">
                    R$ {{ number_format(abs($item['valor']), 2, ',', '.') }}
                </td>
                <td>{{ $item['data'] }}</td>
                <td>{{ $item['tipo'] === 'Receita' ? ($item['categoria'] ?? 'N/A') : ($item['tipoDespesa'] ?? 'N/A') }}</td>
                <td>{{ $item['tipo'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 