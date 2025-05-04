<?php

use App\Http\Controllers\SuppliersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\LancamentoFinanceiroController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;





Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role.level:employee'])->name('dashboard');


Route::middleware(['auth', 'verified', 'role.level:employee'])->group(function () {
    Route::get('products/search', [ProductsController::class, 'search'])->name('products.search');
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

// Rotas para Categorias
Route::get('/categories', [LancamentoFinanceiroController::class, 'CategoryList'])->name('categories.index');
Route::post('/categories', [LancamentoFinanceiroController::class, 'storeCategory'])->name('categories.store');
Route::delete('/categories/{id}', [LancamentoFinanceiroController::class, 'destroyCategory'])->name('categories.destroy');

// Rotas para Receitas e Despesas
Route::post('/incomes', [LancamentoFinanceiroController::class, 'storeRevenue'])->name('incomes.store');
Route::post('/expenses', [LancamentoFinanceiroController::class, 'storeExpense'])->name('expenses.store');

// Rotas para Tipos de Despesa
Route::get('/expense-types', [LancamentoFinanceiroController::class, 'expenseTypeList'])->name('expense-types.index');
Route::post('/expense-types', [LancamentoFinanceiroController::class, 'storeExpenseType'])->name('expense-types.store');
Route::delete('/expense-types/{id}', [LancamentoFinanceiroController::class, 'destroyExpenseType'])->name('expense-types.destroy');

Route::middleware(['auth', 'verified', 'role.level:employee'])->group(function () {
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/search-products', [SaleController::class, 'searchProducts'])->name('sales.search-products');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
