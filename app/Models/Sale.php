<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;
    protected $table = 'sales';
    protected $fillable = ['customer_id', 'total_amount'];

    public function scopeLast30Days(Builder $query)
    {
        return $query->whereDate('created_at', '>=', date('Y-m-d', strtotime('-30 days')));
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_sales')
            ->withPivot('quantity', 'subtotal');
    }
}
