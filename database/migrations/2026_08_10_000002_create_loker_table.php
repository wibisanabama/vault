<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loker', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_loker', 10)->unique();
            $table->enum('status', ['tersedia', 'terisi'])->default('tersedia');
            $table->string('lokasi', 50)->default('Zona A');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loker');
    }
};
