<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Carbon; 
use Illuminate\Support\Facades\DB;

class MicroServices{
    public function checkLowStock(Product $product){
        if($product->quantity < 5){
            return[
                'product' => $product,
                'message' => "Estoque baixo! Tem apenas {$product->quantity} restantes.",
           ];
        }
        return null;
    }
    
    public function productMoreSellOfMonth(){
        $year = Sale::latest('created_at')->first();
        $month = Sale::latest('created_at')->first();   

        if(!$month || !$year){
            return null;
        }

        $month = $month->created_at->format('m');
        $year = $year->created_at->format('Y');

        $result = Sale::select('product_id', DB::raw('COUNT(*) as total_amount'))
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->groupBy('product_id')
        ->orderBy('total_amount', 'desc')
        ->first();

        return $result ? Product::find($result->product_id):null;
    }  
    
    public function productMoreSellOfYear(){
        $year = Sale::latest('created_at')->first();

        if(!$year){
            return null;
        }
        
        $year = $year->created_at->format('Y');

        $result = Sale::select('product_id', DB:raw('COUNT(*) as total_amount'))
        ->whereYear('created_at', $year)
        ->groupBy('product_id')
        ->orderBy('total_amount', 'desc')
        ->first();

        return $result ? Product::find($result->product_id):null;
    }
}