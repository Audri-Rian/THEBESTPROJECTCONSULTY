<?php

namespace App\Services;

use App\Models\StockHistory;

class ProductsHistory
{
    public function getProductsHistory()
    {
        return StockHistory::with('product:id,name,price,price_for_sale')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $quantity = $item->quantity;

                $isEntry = $quantity > 0;

                $unitPrice = $isEntry ? $item->product->price : $item->product->price_for_sale;
                $totalPrice = $unitPrice * abs($quantity);

                $price = $isEntry ? -$totalPrice : $totalPrice;

                return [
                    'id' => $item->id,
                    'product' => $item->product->name,
                    'quantity' => $quantity,
                    'type' => $isEntry ? 'Entrada' : 'Saída',
                    'price' => number_format($price, 2, ',', '.'),
                    'date' => $item->created_at->format('d/m/Y H:i'),
                ];
            });
    }
}
