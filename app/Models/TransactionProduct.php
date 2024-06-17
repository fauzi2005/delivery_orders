<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends Model
{
    use HasFactory;

    protected $table = 't_products';

    protected $fillable = [
        'transaction_id',
        'product_id',
        'price',
        'stock_order',
        'total',
    ];

    public function transactionDeliveryOrder()
    {
        return $this->belongsTo(TransactionDeliveryOrder::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(MasterProduct::class, 'product_id');
    }
}
