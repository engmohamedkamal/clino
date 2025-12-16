<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('draft');
    })->name('home');
});

require_once('auth.php');
require_once('admin.php');
require_once('doctor.php');