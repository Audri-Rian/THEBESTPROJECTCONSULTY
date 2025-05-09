<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'verified', 'role.level:employee'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/invoicing', [DashboardController::class, 'faturamento']);
    Route::get('/dashboard/sales', [DashboardController::class, 'vendas']);
});