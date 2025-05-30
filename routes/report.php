<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Reports\ProductHistoryFiltersController;
use App\Http\Controllers\Reports\FinancialEntryFiltersController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rota principal de relatórios
    Route::get('/report', [ReportsController::class, 'index'])->name('report');

    // Rotas para relatórios de produtos
    Route::post('/sales/search-products', [ProductHistoryFiltersController::class, 'search'])->name('reports.products.search');
    Route::post('/reports/export-products', [ProductHistoryFiltersController::class, 'export'])->name('reports.products.export');

    // Rotas para relatórios financeiros
    Route::post('/reports/search-entries', [FinancialEntryFiltersController::class, 'search'])->name('reports.financial.search');
    Route::post('/reports/export-financial', [FinancialEntryFiltersController::class, 'export'])->name('reports.financial.export');
});
