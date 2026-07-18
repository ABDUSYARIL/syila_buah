<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'image',
        'description',
        'price',
        'unit',
        'stock',
        'status',
    ];

    // Accessor for backward compatibility with view keys
    protected $appends = ['img', 'desc', 'sold', 'rating'];

    public function getImgAttribute()
    {
        return $this->image;
    }

    public function getDescAttribute()
    {
        return $this->description;
    }

    public function getSoldAttribute()
    {
        return $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->whereNotIn('status', ['Dibatalkan', 'Menunggu Pembayaran']);
            })
            ->sum('qty') ?: 0;
    }

    public function getRatingAttribute()
    {
        // A stable deterministic rating between 4.5 and 4.9 based on ID
        return 4.5 + (($this->id * 7) % 5) * 0.1;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
