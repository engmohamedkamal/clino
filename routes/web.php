<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorInfoController;
use App\Http\Controllers\DoctorServicesController;
use App\Http\Controllers\Admin\AppointmentController;
Route::middleware(['auth'])->group(function () {
  Route::get('/home', [HomeController::class, "index"])->name('home');
  Route::get('/about', [HomeController::class, "about"])->name('about');
  Route::get('/our-service', [HomeController::class, "service"])->name('our.service');

  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

  Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
  Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
  Route::get('/contact/edit', [ContactController::class, 'edit'])->name('contact.edit');
  Route::put('/contact', [ContactController::class, 'update'])->name('contact.update');
  Route::delete('/contact', [ContactController::class, 'destroy'])->name('contact.destroy');


  Route::get('/appointment', [AppointmentController::class, "index"])->name('appointment');
  // Route::post('/appointment-store', [AppointmentController::class, "store"])->name('appointment.store');
  Route::get('/appointment-show', [AppointmentController::class, "show"])->name('appointment.show');

  Route::get('/doctor', [DoctorInfoController::class, "list"])->name('doctor.list');
  Route::get('/doctor-info/{id}', [DoctorInfoController::class, 'show'])
    ->whereNumber('id')
    ->name('doctor-info.show');

  // الدكتور يعدل خدماته هو (بدون id)
  Route::get('/doctor/services', [DoctorServicesController::class, 'edit'])->name('doctor.services.edit');
  Route::put('/doctor/services', [DoctorServicesController::class, 'update'])->name('doctor.services.update');

  // للأدمن يعدل خدمات دكتور معين
  Route::get('/admin/doctors/{doctorInfo}/services', [DoctorServicesController::class, 'edit'])->name('admin.doctors.services.edit');
  Route::put('/admin/doctors/{doctorInfo}/services', [DoctorServicesController::class, 'update'])->name('admin.doctors.services.update');

  // toggle (اختياري)
  Route::patch('/admin/doctors/{doctorInfo}/services/{service}/toggle', [DoctorServicesController::class, 'toggle'])
    ->name('admin.doctors.services.toggle');

  Route::get('/doctors/{doctor}/availability', [AppointmentController::class, 'doctorAvailability'])
    ->name('doctors.availability');

  Route::get('/services/{service}/doctors', [ServiceController::class, 'doctors'])
    ->name('services.doctors');

});
Route::get('lang/{locale}', action: function ($locale) {
  if (!in_array($locale, ['en', 'ar'])) {
    abort(404);
  }

  session(['locale' => $locale]);
  app()->setLocale($locale);

  return redirect()->back();
})->name('lang.switch');

Route::middleware(['auth'])->group(function () {

    // Products CRUD
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->name('products.show');

    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');

    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');

    // Optional – generate SKU
    Route::get('/products-generate-sku', [ProductController::class, 'generateSku'])
        ->name('products.generateSku');

    Route::post('/products/{product}/decrease', [ProductController::class, 'decreaseQty'])
    ->name('products.decrease');


});

Route::middleware(['auth'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
});

require_once('auth.php');
require_once('admin.php');
require_once('doctor.php');
require_once('patient.php');