<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    use HasFactory;
    protected $table = 'product_sale';
    protected $fillable = [
        'product_id',
        'sale_id',
        'quantity',
        'subtotal',
    ];
}
