<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','admin.area'])->group(function () {
    Route::get('/service', [ServiceController::class, "index"])->name('service');
    Route::post('/service-store', [ServiceController::class, "store"])->name('service.store');
    Route::get('/service-show', [ServiceController::class, "show"])->name('service.show');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');
    Route::get('/service/{id}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    Route::put('/service/{id}', [ServiceController::class, 'update'])->name('service.update');
});