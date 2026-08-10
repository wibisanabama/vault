<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Loker;
use App\Models\Tarif;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Account
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Petugas Account
        User::create([
            'name' => 'Petugas Vault',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // Seed 20 Lockers
        for ($i = 1; $i <= 20; $i++) {
            Loker::create([
                'nomor_loker' => 'L-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'tersedia',
                'lokasi' => $i <= 10 ? 'Zona A (Utama)' : 'Zona B (Samping)',
            ]);
        }

        // Active Tarif Default (Rp 2.000 / jam)
        Tarif::create([
            'nama' => 'Tarif Standard',
            'harga_per_jam' => 2000.00,
            'is_active' => true,
        ]);
    }
}
