<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 20)->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->foreignId('helm_id')->constrained('helm')->onDelete('cascade');
            $table->foreignId('loker_id')->constrained('loker')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('tgl_titip');
            $table->dateTime('tgl_ambil')->nullable();
            $table->enum('status', ['titip', 'ambil', 'batal'])->default('titip');
            $table->decimal('tarif', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
