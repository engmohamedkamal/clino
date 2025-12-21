<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, "index"])->name('home');
    Route::get('/about', [HomeController::class, "about"])->name('about');
    Route::get('/our-service', [HomeController::class, "service"])->name('our.service');

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/contact/edit', [ContactController::class, 'edit'])->name('contact.edit');
    Route::put('/contact', [ContactController::class, 'update'])->name('contact.update');
    Route::delete('/contact', [ContactController::class, 'destroy'])->name('contact.destroy');
});
Route::get('lang/{locale}', function ($locale) {
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