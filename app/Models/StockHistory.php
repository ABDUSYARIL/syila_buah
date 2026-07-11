<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'reference_id',
        'reference_type', // StockEntry, Order, StockAdjustment, Pembatalan
        'qty', // positif/negatif
        'transaction_type' // Stok Masuk, Checkout, Penyesuaian, Pembatalan
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
