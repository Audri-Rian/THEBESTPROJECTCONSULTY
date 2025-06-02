<?php

namespace App\Services;

use App\Models\StockHistory;

class ProductsHistory
{
    public function getProductsHistory()
    {
        return StockHistory::with('product:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $quantity = $item->quantity;
                $isEntry = $quantity > 0;

                // Use historical prices instead of current product prices
                $unitPrice = $isEntry ? $item->price : $item->price_for_sale;
                $totalPrice = $unitPrice * abs($quantity);

                return [
                    'id' => $item->id,
                    'product' => $item->product->name,
                    'quantity' => $quantity,
                    'type' => $isEntry ? 'entrada' : 'saída',
                    'price' => $totalPrice,
                    'date' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}
