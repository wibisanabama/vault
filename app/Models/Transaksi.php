<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'pelanggan_id',
        'helm_id',
        'loker_id',
        'user_id',
        'tgl_titip',
        'tgl_ambil',
        'status',
        'tarif',
    ];

    protected $casts = [
        'tgl_titip' => 'datetime',
        'tgl_ambil' => 'datetime',
        'tarif' => 'decimal:2',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function helm()
    {
        return $this->belongsTo(Helm::class);
    }

    public function loker()
    {
        return $this->belongsTo(Loker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
