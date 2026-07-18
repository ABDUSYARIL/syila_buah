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

    // Relasi ke model Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor dinamis untuk mendapatkan catatan (notes) dari transaksi referensi terkait
    public function getNotesAttribute()
    {
        // 1. Jika log berasal dari stok masuk (StockEntry)
        if ($this->reference_type === 'StockEntry') {
            $entry = \App\Models\StockEntry::with('supplier')->find($this->reference_id);
            return $entry ? 'Pemasok: ' . ($entry->supplier->name ?? 'Default') . ' (' . ($entry->notes ?: 'Tanpa catatan') . ')' : 'Transaksi Stok Masuk';
        } 
        // 2. Jika log berasal dari penyesuaian stok opname (StockAdjustment)
        elseif ($this->reference_type === 'StockAdjustment') {
            $adj = \App\Models\StockAdjustment::find($this->reference_id);
            return $adj ? 'Opname: ' . $adj->type . ' (' . ($adj->notes ?: 'Tanpa catatan') . ')' : 'Penyesuaian Opname';
        } 
        // 3. Jika log berasal dari checkout belanja pelanggan atau pembatalan pesanan
        elseif ($this->reference_type === 'Order') {
            $order = \App\Models\Order::find($this->reference_id);
            if ($order) {
                return $this->qty < 0 
                    ? 'Pesanan pelanggan (Invoice: ' . $order->invoice_no . ')' 
                    : 'Pembatalan Pesanan (Invoice: ' . $order->invoice_no . ')';
            }
            return 'Transaksi Pesanan';
        }
        
        return '-';
    }
}
