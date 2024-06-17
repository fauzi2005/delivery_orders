<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDeliveryDestination extends Model
{
    use HasFactory;

    protected $table = 'm_delivery_destinations';

    protected $fillable = [
        'name',
        'address',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactionDO()
    {
        return $this->hasMany(TransactionDeliveryOrder::class, 'destination_id');
    }
}
