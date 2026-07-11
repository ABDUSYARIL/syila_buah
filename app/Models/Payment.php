<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method', // Transfer Bank, QRIS
        'proof_of_payment',
        'payment_status', // Menunggu, Lunas, Ditolak
        'payment_date',
        'verified_by_user_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
