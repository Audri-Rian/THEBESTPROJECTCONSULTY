<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/financial.php';
require __DIR__ . '/supplier.php';
require __DIR__ . '/sale.php';
require __DIR__ . '/dashboard.php';
require __DIR__ . '/product.php';
require __DIR__ . '/report.php';