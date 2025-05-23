<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LancamentoFinanceiroController;
use App\Http\Controllers\Financial\ListFinancialController;

Route::middleware(['auth', 'verified', 'role.level:admin'])->group(function () {
    Route::get('/LancamentoFinanceiro', [LancamentoFinanceiroController::class, 'index'])->name('LacamentoFinanceiro');

    Route::get('/categories', [LancamentoFinanceiroController::class, 'CategoryList'])->name('categories.index');
    Route::post('/categories', [LancamentoFinanceiroController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{id}', [LancamentoFinanceiroController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/financial-records/incomes', [LancamentoFinanceiroController::class, 'getIncomes'])->name('financialRecord.incomes');
    Route::get('/financial-records/expenses', [LancamentoFinanceiroController::class, 'getExpenses'])->name('financialRecord.expenses');

    Route::post('/incomes', [LancamentoFinanceiroController::class, 'storeRevenue'])->name('incomes.store');
    Route::put('/incomes/{id}', [LancamentoFinanceiroController::class, 'updateIncome'])->name('incomes.update');
    Route::delete('/incomes/{id}', [LancamentoFinanceiroController::class, 'destroyIncome'])->name('incomes.destroy');

    Route::post('/expenses', [LancamentoFinanceiroController::class, 'storeExpense'])->name('expenses.store');
    Route::put('/expenses/{id}', [LancamentoFinanceiroController::class, 'updateExpense'])->name('expenses.update');
    Route::delete('/expenses/{id}', [LancamentoFinanceiroController::class, 'destroyExpense'])->name('expenses.destroy');

    Route::get('/financial/list', [ListFinancialController::class, 'index'])->name('financial.list');
    Route::delete('/incomes/{id}', [LancamentoFinanceiroController::class, 'destroyIncome'])->name('incomes.destroy');
    Route::delete('/expenses/{id}', [LancamentoFinanceiroController::class, 'destroyExpense'])->name('expenses.destroy');

    Route::get('/expense-types', [LancamentoFinanceiroController::class, 'expenseTypeList'])->name('expense-types.index');
    Route::post('/expense-types', [LancamentoFinanceiroController::class, 'storeExpenseType'])->name('expense-types.store');
    Route::delete('/expense-types/{id}', [LancamentoFinanceiroController::class, 'destroyExpenseType'])->name('expense-types.destroy');
});