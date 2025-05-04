<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function faturamento()
    {
        // Agrupar subtotais por mês a partir de products_sales
        $result = DB::table('products_sales')
            ->selectRaw('MONTH(created_at) as mes, SUM(subtotal) as total')
            ->whereNotNull('created_at')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Preenche os valores para todos os 12 meses
        $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $valores = array_fill(0, 12, 0);

        foreach ($result as $r) {
            $valores[$r->mes - 1] = (float) $r->total;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $valores,
            'total' => array_sum($valores),
            'margem' => 35,
            'quantidade' => DB::table('products_sales')->sum('quantity'),
            'produtos' => DB::table('products')->count()
        ]);
    }

    public function vendas()
    {
        $result = DB::table('products_sales')
            ->join('products', 'products.id', '=', 'products_sales.product_id')
            ->select('products.name', DB::raw('SUM(products_sales.subtotal) as total'))
            ->groupBy('products.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json([
            'labels' => $result->pluck('name'),
            'values' => $result->pluck('total'),
        ]);
    }
}
