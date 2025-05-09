<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'address_id',
        'phone',
        'cnpj',
        'status_id',
        'email',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function orders()
    {
        return $this->hasMany(SupplierOrder::class, 'supplier_id');
    }
}
