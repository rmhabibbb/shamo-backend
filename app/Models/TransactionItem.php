<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'transactions_id',
        'users_id',
        'products_id',
        'quantity'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'products_id', 'id');
    }
}
