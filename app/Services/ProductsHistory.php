<?php

namespace App\Services;

use App\Models\ProductSale;

class ProductsHistory
{
    public function getProductsHistory()
    {
        return ProductSale::with([
                'product:id,name',
                'sale:id,created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'product' => $item->product->name,
                    'quantity' => $item->quantity,
                    'total' => number_format($item->subtotal, 2, ',', '.'),
                    'date' => $item->sale->created_at->format('d/m/Y H:i')
                ];
            });
    }
}