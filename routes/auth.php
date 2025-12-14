<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/register',[AuthController::class,'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.store');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::view('/', 'login')->name('/');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
