<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;

Route::middleware(['auth', 'verified', 'role.level:employee'])->group(function () {
    Route::get('products/search', [ProductsController::class, 'search'])->name('products.search');
    Route::get('products/getall', [ProductsController::class, 'getAllProducts'])->name('products.getAll');
    Route::get('products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('products/history', [ProductsController::class, 'history'])->name('products.history');
    Route::post('products/store', [ProductsController::class, 'store'])->name('products.store');
    Route::get('products/{product}', [ProductsController::class, 'getProduct'])->name('products.get');
    Route::put('products/{product}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');
    Route::get('products/history/get', [ProductsController::class, 'getProductsHistory'])->name('products.history.get');
});