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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf,json',
            'productId' => 'nullable|exists:products,id'
        ]);

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = StockHistory::with('product');

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

        $product = $request->productId ? Product::find($request->productId) : null;
        $filename = 'historico-produtos' . ($product ? '-' . $product->name : '');

        switch ($request->format) {
            case 'xlsx':
                return Excel::download(
                    new ProductHistoryExport($reportData),
                    $filename . '.xlsx'
                )->deleteFileAfterSend(true);

            case 'csv':
                $response = $this->exportCsv($reportData);
                return $response->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');

            case 'pdf':
                $pdf = PDF::loadView('reports.product-history', $reportData);
                return $pdf->download($filename . '.pdf');

            case 'json':
                return response()->json($reportData, 200, [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '.json"',
                    'Access-Control-Expose-Headers' => 'Content-Disposition'
                ]);
        }
    }

    private function exportCsv($reportData)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Encoding' => 'UTF-8',
            'Pragma' => 'public',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://temp', 'w+');
            // Adiciona BOM para UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['Relatório: ' . $reportData['metadata']['report_name']]);
            fputcsv($file, ['Gerado em: ' . $reportData['metadata']['generated_at']]);
            fputcsv($file, ['Total de registros: ' . $reportData['metadata']['total_records']]);
            fputcsv($file, []); // Linha em branco
            
            fputcsv($file, ['ID', 'Produto', 'Quantidade', 'Preço', 'Tipo', 'Data']);
            
            foreach ($reportData['data'] as $row) {
                fputcsv($file, [
                    $row['id'],
                    $row['product_name'],
                    $row['quantity'],
                    'R$ ' . number_format($row['price'], 2, ',', '.'),
                    $row['type'],
                    $row['date']
                ]);
            }
            
            rewind($file);
            $csv = stream_get_contents($file);
            fclose($file);
            
            return $csv;
        };

        return response($callback(), 200, $headers);
    }
}
