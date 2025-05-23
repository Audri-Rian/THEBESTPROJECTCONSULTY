<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Reports\ProductHistoryFiltersController;

Route::get('/report', [ReportsController::class, 'index'])->name('report');
Route::post('/reports/product-history', [ProductHistoryFiltersController::class, 'generate']);
Route::get('/products/search', [ProductHistoryFiltersController::class, 'search']);
Route::get('/products/validate', [ProductHistoryFiltersController::class, 'validateProduct']);
