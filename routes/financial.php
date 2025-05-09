<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LancamentoFinanceiroController;

Route::middleware(['auth', 'verified', 'role.level:admin'])->group(function () {
    Route::get('/LancamentoFinanceiro', [LancamentoFinanceiroController::class, 'index'])->name('LacamentoFinanceiro');

    Route::get('/categories', [LancamentoFinanceiroController::class, 'CategoryList'])->name('categories.index');
    Route::post('/categories', [LancamentoFinanceiroController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{id}', [LancamentoFinanceiroController::class, 'destroyCategory'])->name('categories.destroy');

    Route::post('/incomes', [LancamentoFinanceiroController::class, 'storeRevenue'])->name('incomes.store');
    Route::post('/expenses', [LancamentoFinanceiroController::class, 'storeExpense'])->name('expenses.store');

    Route::get('/expense-types', [LancamentoFinanceiroController::class, 'expenseTypeList'])->name('expense-types.index');
    Route::post('/expense-types', [LancamentoFinanceiroController::class, 'storeExpenseType'])->name('expense-types.store');
    Route::delete('/expense-types/{id}', [LancamentoFinanceiroController::class, 'destroyExpenseType'])->name('expense-types.destroy');
});