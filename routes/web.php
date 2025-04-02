<?php

use App\Http\Controllers\SuppliersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\LancamentoFinanceiroController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role.level:employee'])->name('dashboard');


Route::middleware(['auth', 'verified', 'role.level:employee'])->group(function () {
    Route::get('products', [ProductsController::class, 'index'])->name('products.index');
    Route::post('products/store', [ProductsController::class, 'store'])->name('products.store');
    Route::get('products/{product}', [ProductsController::class, 'getProduct'])->name('products.get');
    Route::put('products/{product}', [ProductsController::class, 'update'])->name('products.update');
});

Route::middleware(['auth', 'verified', 'role.level:admin'])->group(function () {
    Route::get('suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::post('suppliers/store', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::put('suppliers/{supplier}', [SuppliersController::class, 'update'])->name('suppliers.update');
});

Route::get('/LancamentoFinanceiro', [LancamentoFinanceiroController::class, 'index'])->name('LacamentoFinanceiro');

//Enviando nova categoria
Route::get('/categories', [LancamentoFinanceiroController::class, 'CategoryList'])->name('categories.index');
Route::post('/categories',[LancamentoFinanceiroController::class, 'store'])->name('categories.store');
Route::delete('/categories/{id}', [LancamentoFinanceiroController::class, 'destroy'])->name('categories.destroy');

//Enviando Incomes
Route::post('/incomes', [LancamentoFinanceiroController::class, 'storeRevenue'])->name('incomes.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
