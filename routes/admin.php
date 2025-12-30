<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;


use App\Http\Controllers\SettingController;
use App\Http\Controllers\ServiceController;
Route::middleware(['auth', 'admin.area'])->group(function () {
  Route::get('/service', [ServiceController::class, 'index'])->name('service.index');

    Route::get('/service/create', [ServiceController::class, 'create'])->name('service.create');
    Route::post('/service', [ServiceController::class, 'store'])->name('service.store');

    // ✅ Bulk Delete لازم قبل routes اللي فيها {service}
    Route::delete('/service/bulk-delete', [ServiceController::class, 'bulkDestroy'])
        ->name('service.bulkDestroy');

    Route::get('/service/{service}/edit', [ServiceController::class, 'edit'])
        ->whereNumber('service')
        ->name('service.edit');

    Route::put('/service/{service}', [ServiceController::class, 'update'])
        ->whereNumber('service')
        ->name('service.update');

    Route::delete('/service/{service}', [ServiceController::class, 'destroy'])
        ->whereNumber('service')
        ->name('service.destroy');
  Route::get('/users', [AuthController::class, 'index'])->name('users.index');        // جدول اليوزرز
  Route::get('/users/create', [AuthController::class, 'register'])->name('users.create'); // صفحة add user (تقدر تعمل create() لو عايز صفحة مستقلة)
  Route::post('/users', [AuthController::class, 'store'])->name('users.store');      // إضافة يوزر بواسطة الأدمن
  Route::get('/users/{id}', [AuthController::class, 'show'])->name('users.show');    // عرض يوزر واحد
  Route::get('/users/{id}/edit', [AuthController::class, 'edit'])->name('users.edit');
  Route::put('/users/{id}', [AuthController::class, 'update'])->name('users.update');
  Route::delete('/users/{id}', [AuthController::class, 'destroy'])->name('users.destroy');

  // Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
  // Route::get('/settings/create', [SettingController::class, 'create'])->name('settings.create');
  // Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
  // Route::get('/settings/edit', [SettingController::class, 'edit'])->name('settings.edit');
  // Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
  Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');   // نفس الصفحة (create/edit)
  Route::post('/settings', [SettingController::class, 'store'])->name('settings.store'); // إنشاء مرة واحدة
  Route::put('/settings/{setting}', [SettingController::class, 'update'])->name('settings.update'); // تحديث

});
Route::middleware(['auth', 'admin_or_doctor'])->group(function () {
  Route::delete('/patients/bulk-destroy', [PatientController::class, 'bulkDestroy'])
    ->name('patients.bulkDestroy');

  Route::resource('patients', PatientController::class);
});
