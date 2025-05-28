<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use App\Exports\ProductHistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProductHistoryFiltersController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('search');

        if (!$query) {
            $products = Product::with('supplier')->get();
            return response()->json(['products' => $products]);
        }

        $products = Product::with('supplier')
            ->where('name', 'like', '%' . $query . '%')
            ->orWhere('id', 'like', '%' . $query . '%')
            ->get();

        return response()->json(['products' => $products]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf,json',
            'productId' => 'nullable|exists:products,id'
        ]);

        $query = StockHistory::with('product');

        // Se um produto específico foi selecionado
        if ($request->productId) {
            $query->where('product_id', $request->productId);
        }

        $data = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product ? $item->product->name : 'Produto não encontrado',
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'type' => $item->product ? $item->product->type : 'N/A',
                'date' => Carbon::parse($item->created_at)->format('d/m/Y H:i:s')
            ];
        });

        $reportData = [
            'metadata' => [
                'report_name' => 'Histórico de Produtos',
                'generated_at' => now()->format('d/m/Y H:i:s'),
                'filters' => [
                    'product_id' => $request->productId ?? 'Todos'
                ],
                'total_records' => $data->count()
            ],
            'data' => $data
        ];

        switch ($request->format) {
            case 'xlsx':
                return Excel::download(
                    new ProductHistoryExport($reportData),
                    'historico-produtos.xlsx'
                );

            case 'csv':
                return $this->exportCsv($reportData);

            case 'pdf':
                return $this->exportPdf($reportData);

            case 'json':
                return response()->json($reportData)
                    ->header('Content-Disposition', 'attachment; filename="historico-produtos.json"');
        }
    }

    private function exportCsv($reportData)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="historico-produtos.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Relatório: ' . $reportData['metadata']['report_name']]);
            fputcsv($file, ['Gerado em: ' . $reportData['metadata']['generated_at']]);
            fputcsv($file, ['Total de registros: ' . $reportData['metadata']['total_records']]);
            fputcsv($file, []); // Linha em branco
            
            // Cabeçalhos
            fputcsv($file, ['ID', 'Produto', 'Quantidade', 'Preço', 'Tipo', 'Data']);
            
            // Dados
            foreach ($reportData['data'] as $row) {
                fputcsv($file, [
                    $row['id'],
                    $row['product_name'],
                    $row['quantity'],
                    $row['price'],
                    $row['type'],
                    $row['date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPdf($reportData)
    {
        $pdf = PDF::loadView('reports.product-history', $reportData);
        return $pdf->download('historico-produtos.pdf');
    }
}
