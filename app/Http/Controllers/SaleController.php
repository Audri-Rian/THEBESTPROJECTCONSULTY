<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\ProductSale;
use App\Models\Product;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index()
    {
        return Inertia::render('SalesManager');
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'products' => 'required|array',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ]);
    
    $totalAmount = 0;

    $sale = Sale::create([
        'total_amount' => 0,
    ]);

    foreach ($validatedData['products'] as $product) {
        $productId = $product['product_id'];
        $quantity = $product['quantity'];

        $productModel = Product::find($productId);
        
        if ($productModel->quantity < $quantity) {
            return response()->json([
                'message' => "Not enough stock for product: {$productModel->name}. Available: {$productModel->quantity}",
            ], 400);
        }

        $price = $productModel->price_for_sale;
        $subtotal = $price * $quantity;

        ProductSale::create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'sale_id' => $sale->id,
            'subtotal' => $subtotal,
        ]);

        $productModel->decrement('quantity', $quantity);

        $totalAmount += $subtotal;
    }

    $sale->update([
        'total_amount' => $totalAmount,
    ]);

    return response()->json([
        'message' => 'Sale created successfully',
        'sale' => $sale,
    ]);
}

    

    public function searchProducts(Request $request)
    {
        $search = $request->input('search');

        $products = Product::query()
            ->where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'quantity']); 

        return response()->json($products);
    }
}
