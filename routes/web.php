<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DashboardKasirController;

Route::get('/', function () {
    return redirect()->route('produk.index');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');
    Route::get('/riwayat-transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/riwayat-transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::get('/riwayat-transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/riwayat-transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])->name('transaksi.struk');
    Route::get('/dashboard-kasir', [DashboardKasirController::class, 'index'])->name('dashboard-kasir.index');
    
    // Hanya admin yang boleh Tambah/Edit/Hapus
    Route::middleware(['admin'])->group(function () {
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');

        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
        Route::get('/produk/export', [ProdukController::class, 'export'])->name('produk.export');
        Route::post('/produk/import', [ProdukController::class, 'import'])->name('produk.import');
    });
});

require __DIR__ . '/auth.php';
