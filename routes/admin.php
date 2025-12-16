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
    
    Route::get('/about', [AboutController::class, "index"])->name('about');
    Route::post('/about-store', [AboutController::class, "store"])->name('about.store');
    Route::get('/about-show', [AboutController::class, "show"])->name('about.show');
    Route::delete('/abouts/{about}', [AboutController::class, 'destroy'])->name('about.destroy');
    Route::get('/about/{id}/edit', [AboutController::class, 'edit'])->name('about.edit');
    Route::put('/about/{id}', [AboutController::class, 'update'])->name('about.update');
});