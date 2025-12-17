<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
  Route::get('/home', [HomeController::class, "index"])->name('home');
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