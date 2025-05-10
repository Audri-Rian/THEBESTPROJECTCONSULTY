<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Exibe a visão do Dashboard
    public function index()
    {
        return inertia('Dashboard');
    }

    // Retorna dados de faturamento para o dashboard
    public function faturamento()
    {
        // Obtém os dados de faturamento por mês
        $result = DB::table('products_sales')
            ->selectRaw('MONTH(created_at) as mes, SUM(subtotal) as total')
            ->whereNotNull('created_at')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Inicializa os meses e valores
        $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $valores = array_fill(0, 12, 0);

        // Preenche os valores de faturamento
        foreach ($result as $r) {
            $valores[$r->mes - 1] = (float) $r->total;
        }

        // Calcula o custo total
        $totalCusto = DB::table('products_sales')
            ->leftJoin('products', 'products.id', '=', 'products_sales.product_id')
            ->selectRaw('SUM(COALESCE(products.price, 0) * products_sales.quantity) as custo_total')
            ->value('custo_total');

        // Calcula o faturamento total e o lucro total
        $totalFaturado = array_sum($valores);
        $lucroTotal = $totalFaturado - $totalCusto;

        // Calcula o ROI (Retorno sobre o Investimento)
        $roi = $totalCusto > 0 ? round(($lucroTotal / $totalCusto) * 100, 2) : 0;

        // Inicializa os lucros mensais
        $lucrosMensais = array_fill(0, 12, 0);

        // Obtém os lucros mensais
        $detalhesMes = DB::table('products_sales')
            ->join('products', 'products.id', '=', 'products_sales.product_id')
            ->selectRaw('MONTH(products_sales.created_at) as mes, SUM(products_sales.subtotal) as total_faturado, SUM(products.price * products_sales.quantity) as custo_total')
            ->whereNotNull('products_sales.created_at')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Preenche os lucros mensais
        foreach ($detalhesMes as $mes) {
            $lucrosMensais[$mes->mes - 1] = (float) $mes->total_faturado - (float) $mes->custo_total;
        }

        // Retorna os dados como resposta JSON
        return response()->json([
            'labels' => $labels,
            'values' => $valores,
            'total' => array_sum($valores),
            'margem' => array_sum($valores) > 0 ? round(((array_sum($valores) - $totalCusto) / array_sum($valores)) * 100, 2) : 0,
            'quantidade' => DB::table('products_sales')->sum('quantity'),
            'produtos' => DB::table('products')->where('status_id', 1)->count(),
            'lucro_total' => $lucroTotal,
            'roi' => $roi,
            'lucros_mensais' => $lucrosMensais,
        ]);
    }

    // Retorna os dados de vendas para o dashboard
    public function vendas()
    {
        // Obtém os dados de vendas dos produtos
        $result = DB::table('products_sales')
            ->join('products', 'products.id', '=', 'products_sales.product_id')
            ->select('products.name', DB::raw('SUM(products_sales.subtotal) as total'))
            ->groupBy('products.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Retorna os dados de vendas como resposta JSON
        return response()->json([
            'labels' => $result->pluck('name'),
            'values' => $result->pluck('total'),
        ]);
    }

    // Retorna dados de receitas extras para o dashboard
    public function incomes()
    {
        // Obtém as receitas extras
        $receitasExtras = DB::table('incomes')
            ->where('description', 'extra')
            ->sum('amount');
    
        // Retorna as receitas extras como resposta JSON
        return response()->json([
            'receitas_extras' => $receitasExtras ?: 0,
        ]);
    }
}