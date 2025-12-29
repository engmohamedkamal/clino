<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;

use App\Http\Controllers\PatientController;
Route::middleware(['auth', 'admin.area'])->group(function () {
  Route::get('/service', [ServiceController::class, "index"])->name('service');
  Route::post('/service-store', [ServiceController::class, "store"])->name('service.store');
  Route::get('/service-show', [ServiceController::class, "show"])->name('service.show');
  Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');
  Route::get('/service/{id}/edit', [ServiceController::class, 'edit'])->name('service.edit');
  Route::put('/service/{id}', [ServiceController::class, 'update'])->name('service.update');

  Route::get('/users', [AuthController::class, 'index'])->name('users.index');        // جدول اليوزرز
  Route::get('/users/create', [AuthController::class, 'register'])->name('users.create'); // صفحة add user (تقدر تعمل create() لو عايز صفحة مستقلة)
  Route::post('/users', [AuthController::class, 'store'])->name('users.store');      // إضافة يوزر بواسطة الأدمن
  Route::get('/users/{id}', [AuthController::class, 'show'])->name('users.show');    // عرض يوزر واحد
  Route::get('/users/{id}/edit', [AuthController::class, 'edit'])->name('users.edit');
  Route::put('/users/{id}', [AuthController::class, 'update'])->name('users.update');
  Route::delete('/users/{id}', [AuthController::class, 'destroy'])->name('users.destroy');

  Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
  Route::get('/settings/create', [SettingController::class, 'create'])->name('settings.create');
  Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
  Route::get('/settings/edit', [SettingController::class, 'edit'])->name('settings.edit');
  Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
 
});
Route::middleware(['auth', 'admin_or_doctor'])->group(function () {
    Route::delete('/patients/bulk-destroy', [PatientController::class, 'bulkDestroy'])
        ->name('patients.bulkDestroy');

    Route::resource('patients', PatientController::class);
});
