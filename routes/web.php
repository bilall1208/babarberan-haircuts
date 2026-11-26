<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');

Route::prefix('admin')->group(function () {

    Route::middleware(['guest', \App\Http\Middleware\NoCache::class])->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    Route::middleware(['auth', \App\Http\Middleware\NoCache::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        // ---- Admin + Kasir ----
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('admin.transaksi.index');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('admin.transaksi.store');
        Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('admin.transaksi.show');
        Route::get('/transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])->name('admin.transaksi.struk');

        // ---- Khusus Admin ----
        Route::middleware('role:admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

            Route::get('/transaksi/{transaksi}/ubah', [TransaksiController::class, 'edit'])->name('admin.transaksi.edit');
            Route::put('/transaksi/{transaksi}', [TransaksiController::class, 'update'])->name('admin.transaksi.update');
            Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('admin.transaksi.destroy');

            Route::get('/layanan', [LayananController::class, 'index'])->name('admin.layanan.index');
            Route::get('/layanan/tambah', [LayananController::class, 'create'])->name('admin.layanan.create');
            Route::post('/layanan', [LayananController::class, 'store'])->name('admin.layanan.store');
            Route::get('/layanan/{layanan}/ubah', [LayananController::class, 'edit'])->name('admin.layanan.edit');
            Route::put('/layanan/{layanan}', [LayananController::class, 'update'])->name('admin.layanan.update');
            Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])->name('admin.layanan.destroy');

            Route::get('/profil', [ProfilController::class, 'edit'])->name('admin.profil.edit');
            Route::put('/profil', [ProfilController::class, 'update'])->name('admin.profil.update');

            Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
            Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('admin.laporan.cetak');
            Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('admin.laporan.pdf');
            Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('admin.laporan.excel');
        });
    });
});