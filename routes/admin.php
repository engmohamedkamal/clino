<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\DoctorServicesBulkController;
use App\Http\Controllers\Admin\AppointmentController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin.area'])
  ->group(function () {

    // Messages
    Route::get('/messages', [ContactController::class, 'show'])->name('messages.index');
    Route::delete('/messages/bulk-destroy', [ContactController::class, 'bulkDestroy'])->name('messages.bulkDestroy');

    // Services
    Route::get('/service', [ServiceController::class, 'index'])->name('service.index');
    Route::get('/service/create', [ServiceController::class, 'create'])->name('service.create');
    Route::post('/service', [ServiceController::class, 'store'])->name('service.store');

    // ✅ Bulk delete قبل {service}
    Route::delete('/service/bulk-delete', [ServiceController::class, 'bulkDestroy'])->name('service.bulkDestroy');

    Route::get('/service/{service}/edit', [ServiceController::class, 'edit'])
      ->whereNumber('service')->name('service.edit');

    Route::put('/service/{service}', [ServiceController::class, 'update'])
      ->whereNumber('service')->name('service.update');

    Route::delete('/service/{service}', [ServiceController::class, 'destroy'])
      ->whereNumber('service')->name('service.destroy');

 
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::put('/settings/{setting}', [SettingController::class, 'update'])->name('settings.update');

    // Bulk doctor services
    Route::get('/doctors/services/bulk', [DoctorServicesBulkController::class, 'bulkEdit'])
      ->name('doctors.services.bulkEdit');

    Route::put('/doctors/services/bulk', [DoctorServicesBulkController::class, 'bulkUpdate'])
      ->name('doctors.services.bulkUpdate');
  });

/*
|--------------------------------------------------------------------------
| Shared Routes (Secretary / Doctor)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'secretary_or_doctor'])->group(function () {

  // Patients
  Route::delete('/patients/bulk-destroy', [PatientController::class, 'bulkDestroy'])->name('patients.bulkDestroy');
  Route::get('/patients/cards', [PatientController::class, 'cards'])->name('patients.cards');
  Route::resource('patients', PatientController::class);

  // Medical Orders
  Route::delete('/medical-orders/bulk-destroy', [MedicineController::class, 'bulkDestroy'])->name('medical-orders.bulkDestroy');
  Route::resource('medical-orders', MedicineController::class);

  // Appointments (extra actions)
  Route::get('/appointments/{id}', [AppointmentController::class, 'singleShow'])
    ->whereNumber('id')->name('appointments.singleShow');

  Route::get('/appointments/{appointment}/vip-print', [AppointmentController::class, 'vipPrint'])
    ->name('appointment.vipPrint');

  Route::get('/appointments/{appointment}/reset', [AppointmentController::class, 'reset'])
    ->name('appointment.reset');

  Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
    ->name('appointments.updateStatus');

  Route::get('/day-summary', [AppointmentController::class, 'daySummary'])->name('day.summary');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (All logged-in users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

  // Booking + list (حسب نظامك الحالي)
  Route::get('/appointments/book', [AppointmentController::class, 'index'])->name('appointment.index');
  Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointment.store');
  Route::get('/appointments', [AppointmentController::class, 'show'])->name('appointment.show');

  // Only secretary/doctor can edit/delete appointments
  Route::middleware(['secretary_or_doctor'])->group(function () {

    Route::get('/appointments/{id}/edit', [AppointmentController::class, 'edit'])
      ->whereNumber('id')->name('appointments.edit');

    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])
      ->whereNumber('id')->name('appointments.update');

    Route::delete('/appointments/bulk-destroy', [AppointmentController::class, 'bulkDestroy'])
      ->name('appointments.bulkDestroy');

    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])
      ->whereNumber('id')->name('appointments.destroy');
  });
});


Route::middleware(['auth', 'secretary_or_admin'])
  ->group(function () {
    Route::get('/users', [AuthController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AuthController::class, 'register'])->name('users.create');
    Route::post('/users', [AuthController::class, 'store'])->name('users.store');
    Route::delete('/users/bulk-destroy', [AuthController::class, 'bulkDestroy'])->name('users.bulkDestroy');
    Route::get('/users/{id}', [AuthController::class, 'show'])->name('users.show');
    Route::delete('/users/{id}', [AuthController::class, 'destroy'])->name('users.destroy');
  });