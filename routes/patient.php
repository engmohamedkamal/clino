<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patient\FeedbackController;
use App\Http\Controllers\Patient\PatientInfoController;
use App\Http\Controllers\UserInfoController;

Route::middleware(['auth'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, "index"])->name('feedback.form');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/my/patient-info/{id}', [UserInfoController::class, 'myInfo'])
    ->name('patient-info.my');
    Route::resource('patient-info', UserInfoController::class);
});