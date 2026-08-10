<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loker extends Model
{
    use HasFactory;

    protected $table = 'loker';

    protected $fillable = [
        'nomor_loker',
        'status',
        'lokasi',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
