<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->decimal('jumlah', 10, 2);
            $table->enum('metode_bayar', ['tunai', 'ewallet'])->default('tunai');
            $table->dateTime('tgl_bayar');
            $table->enum('status', ['lunas', 'belum'])->default('lunas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
