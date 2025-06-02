<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Histórico de Produtos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .metadata {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $metadata['report_name'] }}</h1>
    </div>

    <div class="metadata">
        <p><strong>Data de Geração:</strong> {{ $metadata['generated_at'] }}</p>
        <p><strong>Total de Registros:</strong> {{ $metadata['total_records'] }}</p>
        <p><strong>Filtro de Produto:</strong> {{ $metadata['filters']['product_id'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Tipo</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item['id'] }}</td>
                <td>{{ $item['product_name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                <td>{{ $item['type'] }}</td>
                <td>{{ $item['date'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Relatório gerado automaticamente pelo sistema</p>
    </div>
</body>
</html>