<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $table = 'tarif';

    protected $fillable = [
        'nama',
        'harga_per_jam',
        'is_active',
    ];

    protected $casts = [
        'harga_per_jam' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
