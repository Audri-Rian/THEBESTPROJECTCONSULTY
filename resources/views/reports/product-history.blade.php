<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Histórico de Produtos</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .metadata { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Histórico de Produtos</h1>
        <p>Gerado em: {{ \Carbon\Carbon::parse($metadata['generated_at'])->format('d/m/Y H:i') }}</p>
    </div>

    <div class="metadata">
        <h3>Filtros Aplicados:</h3>
        <ul>
            @foreach(array_filter($metadata['filters']) as $key => $value)
                <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
        <p>Total de Registros: {{ $metadata['total_records'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
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
                <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>