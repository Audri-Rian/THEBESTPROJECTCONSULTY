<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'date',
        'categories_id',
        'expense_types_id'
    ];
    
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }
}