<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\DoctorInfoController;
use App\Http\Controllers\ReportController;


Route::middleware(['auth', 'doctor.area'])->group(function () {
    Route::get('/my-info', [UserInfoController::class, 'show'])->name('my-info.show');

    // إضافة لأول مرة فقط
    Route::get('/my-info/create', [UserInfoController::class, 'create'])->name('my-info.create');
    Route::post('/my-info', [UserInfoController::class, 'store'])->name('my-info.store');

    // تحديث فقط
    Route::get('/my-info/edit', [UserInfoController::class, 'edit'])->name('my-info.edit');
    Route::put('/my-info', [UserInfoController::class, 'update'])->name('my-info.update');


});

Route::middleware(['auth', 'admin_or_doctor'])->group(function () {

    Route::get('/doctor-info/create', [DoctorInfoController::class, 'create'])
        ->name('doctor-info.create');

    Route::post('/doctor-info', [DoctorInfoController::class, 'store'])
        ->name('doctor-info.store');

    Route::get('/doctor-info/{doctorInfo}/edit', [DoctorInfoController::class, 'edit'])
        ->name('doctor-info.edit');

    Route::put('/doctor-info/{doctorInfo}', [DoctorInfoController::class, 'update'])
        ->name('doctor-info.update');
});






Route::middleware(['auth', 'admin_or_doctor'])->prefix('admin')->group(function () {

    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    
    // ✅ خليها reports.show
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
});
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');


Route::middleware(['auth'])->group(function () {
    Route::get('/reports/{report}', [ReportController::class, 'show'])
        ->name('reports.show');
});

