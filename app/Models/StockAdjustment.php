<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'qty',
        'difference',
        'type', // Buah Busuk, Buah Rusak, Penyusutan, Kehilangan
        'notes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
