<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserInfoController;

Route::middleware(['auth','doctor.area'])->group(function () {
    Route::get('/my-info', [UserInfoController::class, 'show'])->name('my-info.show');

    // إضافة لأول مرة فقط
    Route::get('/my-info/create', [UserInfoController::class, 'create'])->name('my-info.create');
    Route::post('/my-info', [UserInfoController::class, 'store'])->name('my-info.store');

    // تحديث فقط
    Route::get('/my-info/edit', [UserInfoController::class, 'edit'])->name('my-info.edit');
    Route::put('/my-info', [UserInfoController::class, 'update'])->name('my-info.update');
});