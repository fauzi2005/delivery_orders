<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDeliveryOrder extends Model
{
    use HasFactory;

    protected $table = 't_delivery_orders';

    protected $fillable = [
        'transaction_number',
        'date',
        'destination_id',
        'total',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryDestination()
    {
        return $this->belongsTo(MasterDeliveryDestination::class, 'destination_id');
    }

    public function transactionProduct()
    {
        return $this->hasMany(TransactionProduct::class, 'transaction_id');
    }

    public function documentReference()
    {
        return $this->hasMany(TransactionDocumentReference::class, 'delivery_order_id');
    }
}
