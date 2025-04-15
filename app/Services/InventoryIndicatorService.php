<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class InventoryIndicatorService{

    protected $product;
    protected $sale;
    protected $supplier;

    public function __construct(Product $product, Sale $sale, Supplier $supplier){
        $this->product = $product;
        $this->sale = $sale;
        $this->supplier = $supplier;
    }


    //calculando o giro de estoque, sendo a media de estoque a atual pois ainda não girou a mercadoria
    public function calculateStockTurnover(){
        $totalCosts = $this->sale->sum('total_amount'); //utilizar o custo total de vendas
        $stockAvg = $this->product->count();
        if ($stockAvg == 0) {
            return 0; // Evitar divisão por zero
        }
        $stockTurnover = $totalCosts / $stockAvg;
        return $stockTurnover;
    }

    public function findIdleProducts(){
        $limitDate = now()->subDays(30); 

        $soldProducts = $this->sale
        ->whereDate('created_at', '>=', $limitDate)
        ->pluck('product_id')
        ->unique()->toArray(); //pegar os produtos vendidos nos ultimos 30 dias

        $idleProducts = $this->product
        ->whereNotIn('id', $soldProducts)
        ->get(); //pegar os produtos que não foram vendidos nos ultimos 30 dias
        
        $idleProductsCount = $idleProducts->count(); //contar os produtos que não foram vendidos

        return [
            'idle_products' => $idleProducts,
            'count' => $idleProductsCount,
        ];
    }

    public function calculateStockCoverage(){
        $date = date('Y-m-d', strtotime('-30 days')); //data de 30 dias atrás
        $stock = $this->product->sum('quantity'); //contar a quantidade de produtos em estoque
        $saleAvg = $this->sale
        ->whereDate('created_at', '>=', $date)
        ->sum('quantity')/30; //calcular a media de vendas

        if ($saleAvg == 0) {
            return 0; // Evitar divisão por zero
        }
        $stockCoverage = $stock / $saleAvg; //calcular a cobertura de estoque
        return $stockCoverage;
    }

    //tabela sale tem que estar relacionada com a tabela product

    public function calculateABCcurve(){
        $sales = $this->sale->with('product')->get();

        $productTotals = [];

        foreach($sales as $sale){
            $product = $sale->product;

            if(!$product) continue; // Verifica se o produto existe

            $productId = $product->id;
            $valueTotal = $product->price * $sale->quantity; //calcular o valor total de vendas
            if(!isset($productTotals[$productId])){
                $productTotals[$productId] = [
                    'product_name' => $product->name,
                    'total' => 0,
                ];
            }
            $productTotals[$productId]['total'] += $valueTotal; //soma o valor total de vendas
            
        }

        uasort($productTotals, function($a, $b){
            return $b['total'] <=> $a['total']; // ordena do maior para o menor
        });

        $grandTotal = array_sum(array_column($productTotals, 'total')); //calcular o total geral de vendas

        $accumulated = 0; //variavel para calcular o percentual acumulado
        foreach($productTotals as $productTotal){
            $percentage = ($productTotal['total'] / $grandTotal) * 100; //calcular o percentual de cada produto
            $accumulated += $percentage; //calcular o percentual acumulado

            if($accumulated <= 80){
                $class = 'A';
            } elseif($accumulated <= 95){
                $class = 'B';
            } else {
                $class = 'C';
            }

            $productTotal['percentage'] = round($percentage, 2);
            $productTotal['accumulated'] = round($accumulated, 2);
            $productTotal['class'] = $class;
        }
        return $productTotals; //retorna o array com os produtos e suas respectivas classes
    }

    public function calculateReorderPoint(){ 
        $salesAvg = $this->sale->with('product')->avg('quantity'); //calcular a media de vendas
        $deliveries = SupplierOrder::whereNotNull('delivery_date')->get(); //buscar os pedidos de compra

        $totalDays = 0;
        $orderCount = 0;

        foreach($deliveries as $delivery){
            $days = $delivery->created_at->diffInDays($delivery->delivery_date); 
            $totalDays += $days; //soma os dias de entrega
            $orderCount++;
        }
        $leadTime = 0;
        if($orderCount > 0){
            $leadTime = $totalDays / $orderCount; //calcular o tempo medio de entrega
        }

        $reorderPoint = $salesAvg * $leadTime; //calcular o ponto de reposição
        return $reorderPoint; //retorna o ponto de reposição
    }
}