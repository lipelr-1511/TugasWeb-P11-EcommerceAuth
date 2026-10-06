<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('products.index'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profil (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Kelola produk: hanya admin & editor (otorisasi per-record oleh ProductPolicy di controller).
// PENTING: didaftarkan SEBELUM route show agar /products/create tidak ditangkap {product}.
Route::middleware(['auth', 'role:admin,editor'])->group(function () {
    Route::resource('products', ProductController::class)->except(['index', 'show']);
});

// Katalog produk: wajib login
Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)->only(['index', 'show']);
});

// Area admin: hanya role admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
