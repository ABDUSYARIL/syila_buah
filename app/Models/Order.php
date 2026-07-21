<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_no',
        'shipping_address',
        'shipping_method',
        'shipping_cost',
        'subtotal',
        'total',
        'status', // Menunggu Pembayaran, Menunggu Verifikasi, Diproses, Dikirim, Selesai, Dibatalkan
        'notes',
        'cancel_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
