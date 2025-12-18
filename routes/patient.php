<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patient\FeedbackController;
use App\Http\Controllers\Patient\PatientInfoController;
Route::middleware(['auth','patient.area'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, "index"])->name('feedback.form');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

       Route::resource('patient-info', PatientInfoController::class);

    // لو حابب Route خاص بالمريض يشوف ويعدل بياناته بس
    Route::get('/my/patient-info', [PatientInfoController::class, 'myInfo'])
        ->name('patient-info.my');
});