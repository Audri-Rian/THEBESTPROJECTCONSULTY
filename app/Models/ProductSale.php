<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    use HasFactory;
    protected $table = 'products_sales';
    protected $fillable = [
        'product_id',
        'sale_id',
        'quantity',
        'subtotal',
    ];
}
