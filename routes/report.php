<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Reports\ProductHistoryFiltersController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/report', [ReportsController::class, 'index'])->name('report');
    Route::post('/sales/search-products', [ProductHistoryFiltersController::class, 'search'])->name('reports.products.search');
    Route::post('/reports/export-products', [ProductHistoryFiltersController::class, 'export'])->name('reports.products.export');
});
