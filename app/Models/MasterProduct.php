<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProduct extends Model
{
    use HasFactory;

    protected $table = 'm_products';

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'stock',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactionProduct()
    {
        return $this->hasMany(TransactionProduct::class, 'product_id');
    }
}
