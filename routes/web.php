<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
// TAMBAHKAN INI jika kamu punya CartController terpisah
// use App\Http\Controllers\CartController; 
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PROFILE
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // PRODUCTS (CRUD)
    Route::resource('categories', CategoryController::class);
    Route::delete('/products/batch-delete', [ProductController::class, 'batchDelete'])->name('products.batchDelete');
    Route::resource('products', ProductController::class);

    // TRANSACTIONS & EXPORT
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/export', [TransactionController::class, 'export'])->name('export'); // Pindahkan ke sini agar rapi
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::delete('/reset', [TransactionController::class, 'reset'])->name('reset');
        Route::post('/store', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
    });
    // Route untuk Pengaturan Toko
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // CART (Digabung ke TransactionController agar simpel)
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('/add', [TransactionController::class, 'addToCart'])->name('add');
        Route::post('/remove', [TransactionController::class, 'removeFromCart'])->name('remove');
        Route::post('/update', [TransactionController::class, 'updateCart'])->name('update');
        
        // Perbaikan: Arahkan ke TransactionController saja jika tidak ada CartController
        Route::post('/clear', [TransactionController::class, 'clearCart'])->name('clear');
        
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    
    });
});

require __DIR__.'/auth.php';