<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuppliersController;

Route::middleware(['auth', 'verified', 'role.level:admin'])->group(function () {
    Route::get('suppliers/getall', [SuppliersController::class, 'getAllSuppliers'])->name('suppliers.getAll');
    Route::get('suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::post('suppliers/store', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::put('suppliers/{supplier}', [SuppliersController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{supplier}', [SuppliersController::class, 'destroy'])->name('suppliers.destroy');
});