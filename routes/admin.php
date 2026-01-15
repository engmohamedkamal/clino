<?php

use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DoctorServicesBulkController;

Route::middleware(['auth', 'admin.area'])->group(function () {
  Route::get('/messages', [ContactController::class, 'show'])->name('messages.index');
  Route::delete('/messages/bulk-destroy', [ContactController::class, 'bulkDestroy'])
    ->name('messages.bulkDestroy');
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
  // Users list + search
  Route::get('/users', [AuthController::class, 'index'])
    ->name('users.index');

  // Add user page
  Route::get('/users/create', [AuthController::class, 'register'])
    ->name('users.create');

  // Store user
  Route::post('/users', [AuthController::class, 'store'])
    ->name('users.store');

  // Bulk delete (لازم قبل users/{id})
  Route::delete('/users/bulk-destroy', [AuthController::class, 'bulkDestroy'])
    ->name('users.bulkDestroy');

  // Show single user
  Route::get('/users/{id}', [AuthController::class, 'show'])
    ->name('users.show');

  // Edit user
  Route::get('/users/{id}/edit', [AuthController::class, 'edit'])
    ->name('users.edit');

  Route::put('/users/{id}', [AuthController::class, 'update'])
    ->name('users.update');
  Route::delete('/users/{id}', [AuthController::class, 'destroy'])
    ->name('users.destroy');
  Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');   // نفس الصفحة (create/edit)
  Route::post('/settings', [SettingController::class, 'store'])->name('settings.store'); // إنشاء مرة واحدة
  Route::put('/settings/{setting}', [SettingController::class, 'update'])->name('settings.update'); // تحديث

});
Route::middleware(['auth', 'admin_or_doctor'])->group(function () {
  Route::delete('/patients/bulk-destroy', [PatientController::class, 'bulkDestroy'])
    ->name('patients.bulkDestroy');

  Route::resource('patients', PatientController::class);

  Route::delete('/medical-orders/bulk-destroy', [MedicineController::class, 'bulkDestroy'])
    ->name('medical-orders.bulkDestroy');

  Route::resource('medical-orders', MedicineController::class);
 Route::get('/appointments/{id}', [AppointmentController::class, 'singleShow'])
  ->whereNumber('id')
  ->name('appointments.singleShow');

});

Route::middleware(['auth'])->group(function () {

  Route::get('/appointments/book', [AppointmentController::class, 'index'])
    ->name('appointment.index');

  Route::post('/appointments', [AppointmentController::class, 'store'])
    ->name('appointment.store');

  Route::get('/appointments', [AppointmentController::class, 'show'])
    ->name('appointment.show');

  Route::middleware(['admin_or_doctor'])->group(function () {

    Route::get('/appointments/{id}/edit', [AppointmentController::class, 'edit'])
      ->whereNumber('id')
      ->name('appointments.edit');

    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])
      ->whereNumber('id')
      ->name('appointments.update');


    Route::delete('/appointments/bulk-destroy', [AppointmentController::class, 'bulkDestroy'])
      ->name('appointments.bulkDestroy');

    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])
      ->whereNumber('id')
      ->name('appointments.destroy');
  });

});


Route::middleware(['auth', 'admin_or_doctor'])->group(function () {

  // لو عايزها للأدمن فقط حط middleware admin بدل admin_or_doctor
  Route::get('/admin/doctors/services/bulk', [DoctorServicesBulkController::class, 'bulkEdit'])
    ->name('admin.doctors.services.bulkEdit');

  Route::put('/admin/doctors/services/bulk', [DoctorServicesBulkController::class, 'bulkUpdate'])
    ->name('admin.doctors.services.bulkUpdate');

    Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
    ->name('appointments.updateStatus');


});
