<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Status; // Ensure the Status model exists in this namespace

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'price_for_sale', 
        'quantity', 
        'status_id',
        'supplier_id',
        'image_path'
    ];

    protected $appends = ['image_url'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return asset('images/no-image.png');
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->where('name', 'LIKE', "%{$name}%")
            ->collate('utf8mb4_unicode_ci');
    }
}
