<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $table = 'sales';
    protected $fillable = ['customer_id', 'total_amount'];

    public function scopeLast30Days(Builder $query)
    {
        return $query->whereDate('created_at', '>=', date('Y-m-d', strtotime('-30 days')));
    }
}
