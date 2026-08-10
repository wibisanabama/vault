<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokerController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Transaksi (Petugas & Admin)
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/titip', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi/titip', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{id}/ambil', [TransaksiController::class, 'showAmbilForm'])->name('transaksi.ambil');
    Route::post('/transaksi/{id}/ambil', [TransaksiController::class, 'prosesAmbil'])->name('transaksi.proses-ambil');
    Route::get('/transaksi/{id}/struk', [TransaksiController::class, 'struk'])->name('transaksi.struk');

    // Pelanggan (Petugas & Admin)
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');

    // Loker (Petugas & Admin view, Admin edit)
    Route::get('/loker', [LokerController::class, 'index'])->name('loker.index');
    Route::post('/loker', [LokerController::class, 'store'])->middleware('role:admin')->name('loker.store');
    Route::put('/loker/{id}', [LokerController::class, 'update'])->middleware('role:admin')->name('loker.update');
    Route::delete('/loker/{id}', [LokerController::class, 'destroy'])->middleware('role:admin')->name('loker.destroy');

    // Admin Only Features
    Route::middleware('role:admin')->group(function () {
        // Kelola Petugas
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Kelola Tarif
        Route::get('/tarif', [TarifController::class, 'index'])->name('tarif.index');
        Route::post('/tarif', [TarifController::class, 'store'])->name('tarif.store');
        Route::post('/tarif/{id}/aktifkan', [TarifController::class, 'setActive'])->name('tarif.set-active');
        Route::delete('/tarif/{id}', [TarifController::class, 'destroy'])->name('tarif.destroy');

        // Lihat Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });
});

require __DIR__.'/auth.php';
