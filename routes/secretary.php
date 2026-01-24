<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashController;

Route::middleware(['auth','secretary_or_doctor'])->group(function () {

    // Index + Filters
    Route::get('/cash', [CashController::class, 'index'])
        ->name('cash.index');

    // Create
    Route::get('/cash/create', [CashController::class, 'create'])
        ->name('cash.create');

    // Store
    Route::post('/cash', [CashController::class, 'store'])
        ->name('cash.store');

    // Edit
    Route::get('/cash/{Cash}/edit', [CashController::class, 'edit'])
        ->whereNumber('cash')
        ->name('cash.edit');

    // Update
    Route::put('/cash/{Cash}', [CashController::class, 'update'])
        ->whereNumber('cash')
        ->name('cash.update');

    // Delete single
    Route::delete('/cash/{Cash}', [CashController::class, 'destroy'])
        ->whereNumber('cash')
        ->name('cash.destroy');

    // Bulk delete (لازم قبل أي route فيها {Cash} لو غيرت الترتيب)
    Route::delete('/cash/bulk-destroy', [CashController::class, 'bulkDestroy'])
        ->name('cash.bulkDestroy');
    Route::get('printCash', [CashController::class, 'printCash'])
        ->name('printCash');
});
