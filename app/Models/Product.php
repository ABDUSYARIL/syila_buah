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

    // Accessor for backward compatibility with 'img' key in views
    protected $appends = ['img'];

    public function getImgAttribute()
    {
        return $this->image;
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
