<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashController;

Route::middleware(['auth','secretary_or_doctor'])->group(function () {
    Route::get('/cash', [CashController::class, 'index'])
        ->name('cash.index');
    Route::get('/cash/create', [CashController::class, 'create'])
        ->name('cash.create');
    Route::post('/cash', [CashController::class, 'store'])
        ->name('cash.store');
    Route::get('/cash/{Cash}/edit', [CashController::class, 'edit'])
        ->whereNumber('cash')
        ->name('cash.edit');
    Route::put('/cash/{Cash}', [CashController::class, 'update'])
        ->whereNumber('cash')
        ->name('cash.update');
  Route::delete('cash/{cash}', [CashController::class, 'destroy'])->name('cash.destroy');

    Route::delete('/cash/bulk-destroy', [CashController::class, 'bulkDestroy'])
        ->name('cash.bulkDestroy');
    Route::get('printCash', [CashController::class, 'printCash'])
        ->name('printCash');
});
