<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'transaksi_id',
        'jumlah',
        'metode_bayar',
        'tgl_bayar',
        'status',
    ];

    protected $casts = [
        'tgl_bayar' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
