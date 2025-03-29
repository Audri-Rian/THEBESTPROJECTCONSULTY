<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductsController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role.level:employee'])->name('dashboard');

Route::get('products', [ProductsController::class, 'index'])
    ->middleware(['auth', 'verified', 'role.level:employee'])
    ->name('products');
Route::post('products/store', [ProductsController::class, 'store'])
    ->middleware(['auth', 'verified', 'role.level:employee'])
    ->name('products.store');
Route::get('products/{product}', [ProductsController::class, 'getProduct'])
    ->middleware(['auth', 'verified', 'role.level:employee'])
    ->name('products.get');
Route::put('products/{product}', [ProductsController::class, 'update'])
    ->middleware(['auth', 'verified', 'role.level:employee'])
    ->name('products.update');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
