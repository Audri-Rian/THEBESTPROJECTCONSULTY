<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\MicroServices;

class AlertProductController extends Controller
{
    public function checkStock(int $productId ){
        $microservices = new MicroServices();

        $result = $microservices->checkLowStock(Product::find($productId));

        if(!$result){
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        return response()->json($result);
    }
}
