<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;

Route::middleware(['auth', 'verified', 'role.level:employee'])->group(function () {
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/search-products', [SaleController::class, 'searchProducts'])->name('sales.search-products');
});