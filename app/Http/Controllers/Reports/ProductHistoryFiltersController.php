<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductHistoryExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductHistoryFiltersController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'productName' => 'nullable|string|max:255',
            'format' => 'required|in:xlsx,csv,pdf,json'
        ]);

        $query = StockHistory::with(['product'])
            ->whereHas('product', function ($q) use ($validated) {
                $q->when(!empty($validated['productName']), function ($q) use ($validated) {
                    $q->where('name', 'like', '%' . $validated['productName'] . '%');
                });
            });

        $data = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product ? $item->product->name : 'Produto não encontrado',
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'type' => $item->product ? $item->product->type : 'N/A',
                'date' => $item->created_at->toISOString()
            ];
        });

        $reportData = [
            'metadata' => [
                'report_name' => 'Histórico de Produtos',
                'generated_at' => now()->toISOString(),
                'filters' => array_filter(['productName' => $validated['productName']]),
                'total_records' => count($data)
            ],
            'data' => $data
        ];

        switch ($validated['format']) {
            case 'xlsx':
                return Excel::download(
                    new ProductHistoryExport($reportData),
                    'products-history-report.xlsx'
                );

            case 'csv':
                return $this->generateCsv($reportData);

            case 'pdf':
                return $this->generatePdf($reportData);

            case 'json':
                return response()->json($reportData)
                    ->header('Content-Disposition', 'attachment; filename="products-history-report.json"');

            default:
                abort(400, 'Formato inválido');
        }
    }

    private function generateCsv($reportData)
    {
        $csv = "Relatório: Histórico de Produtos\n";
        $csv .= "Gerado em: " . $reportData['metadata']['generated_at'] . "\n\n";

        $csv .= "Filtros:\n";
        foreach (array_filter($reportData['metadata']['filters']) as $key => $value) {
            $csv .= ucfirst($key) . ": " . $value . "\n";
        }
        $csv .= "\nTotal de Registros: " . $reportData['metadata']['total_records'] . "\n\n";

        $csv .= "ID,Nome,Quantidade,Preço,Tipo,Data\n";

        foreach ($reportData['data'] as $row) {
            $csv .= implode(',', [
                $row['id'],
                '"' . str_replace('"', '""', $row['product_name']) . '"',
                $row['quantity'],
                $row['price'],
                $row['type'],
                $row['date']
            ]) . "\n";
        }

        return response()->streamDownload(
            function () use ($csv) {
                echo $csv;
            },
            'products-history-report.csv',
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="products-history-report.csv"'
            ]
        );
    }

    private function generatePdf($reportData)
    {
        $pdf = Pdf::loadView('reports.product-history', $reportData);
        return $pdf->download('products-history-report.pdf');
    }

    public function search(Request $request)
    {
        $request->validate(['name' => 'required|string|min:2']);

        return response()->json(
            Product::where('name', 'LIKE', "%{$request->name}%")
                ->select('id', 'name', 'price', 'type')
                ->limit(10)
                ->get()
        );
    }
}