<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDocumentReference extends Model
{
    use HasFactory;

    protected $table = 't_document_references';

    protected $fillable = [
        'delivery_order_id',
        'document_url'
    ];

    public function transactionDeliveryOrder()
    {
        return $this->belongsTo(TransactionDeliveryOrder::class, 'delivery_order_id');
    }
}
