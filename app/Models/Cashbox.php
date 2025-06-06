<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashbox extends Model
{
    use HasFactory;

    protected $table = 'cashbox';

    protected $fillable = [
        'name',
        'description',
        'report',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'report' => 'array'
    ];
}