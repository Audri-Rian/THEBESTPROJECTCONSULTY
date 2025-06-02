<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'amount',
        'date',
        'categories_id',
        'report' // Se você ainda estiver usando este campo
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'report' => 'array' // Se você ainda estiver usando este campo
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id'); // Especificar FK
    }
}