<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorInfoController;
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
  Route::post('/appointment-store', [AppointmentController::class, "store"])->name('appointment.store');
  Route::get('/appointment-show', [AppointmentController::class, "show"])->name('appointment.show');
  Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointment.destroy');
  Route::get('/appointment/{id}/edit', [AppointmentController::class, 'edit'])->name('appointment.edit');
  Route::put('/appointment/{id}', [AppointmentController::class, 'update'])->name('appointment.update');
  
  Route::get('/doctor', [DoctorInfoController::class, "list"])->name('doctor.list');
  Route::get('/doctor-info/{id}', [DoctorInfoController::class, 'show'])
        ->whereNumber('id')
        ->name('doctor-info.show');
});
Route::get('lang/{locale}', action: function ($locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);

    return redirect()->back();
})->name('lang.switch');

require_once('auth.php');
require_once('admin.php');
require_once('doctor.php');
require_once('patient.php');