<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helm extends Model
{
    use HasFactory;

    protected $table = 'helm';

    protected $fillable = [
        'pelanggan_id',
        'merk',
        'warna',
        'deskripsi',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
