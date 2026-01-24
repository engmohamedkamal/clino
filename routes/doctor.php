<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\DoctorInfoController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ServiceInvoiceController;
use App\Http\Controllers\PatientTransferController;


Route::middleware(['auth', 'doctor.area'])->group(function () {
    Route::get('/my-info', [UserInfoController::class, 'show'])->name('my-info.show');

    // إضافة لأول مرة فقط
    Route::get('/my-info/create', [UserInfoController::class, 'create'])->name('my-info.create');
    Route::post('/my-info', [UserInfoController::class, 'store'])->name('my-info.store');

    // تحديث فقط
    Route::get('/my-info/edit', [UserInfoController::class, 'edit'])->name('my-info.edit');
    Route::put('/my-info', [UserInfoController::class, 'update'])->name('my-info.update');


});

Route::middleware(['auth', 'admin_or_doctor'])->group(function () {

    Route::get('/doctor-info/create', [DoctorInfoController::class, 'create'])
        ->name('doctor-info.create');

    Route::post('/doctor-info', [DoctorInfoController::class, 'store'])
        ->name('doctor-info.store');

    Route::get('/doctor-info/{doctorInfo}/edit', [DoctorInfoController::class, 'edit'])
        ->name('doctor-info.edit');

    Route::put('/doctor-info/{doctorInfo}', [DoctorInfoController::class, 'update'])
        ->name('doctor-info.update');
});

Route::middleware(['auth', 'secretary_or_doctor'])->prefix('admin')->group(function () {
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
});


Route::middleware(['auth'])->group(function () {
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/{report}', [ReportController::class, 'show'])
    ->name('reports.show');
    // ================= Patient (View only) =================
    Route::get('/prescriptions', [PrescriptionController::class, 'index'])
        ->name('prescriptions.index');

    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])
        ->name('prescriptions.show')
        ->whereNumber('prescription');

    // ================= Admin/Doctor (Manage) =================
    Route::middleware(['admin_or_doctor'])->group(function () {

        Route::get('/prescriptions/create', [PrescriptionController::class, 'create'])
            ->name('prescriptions.create');

        Route::post('/prescriptions', [PrescriptionController::class, 'store'])
            ->name('prescriptions.store');

        Route::get('/prescriptions/{prescription}/edit', [PrescriptionController::class, 'edit'])
            ->name('prescriptions.edit')
            ->whereNumber('prescription');

        Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update'])
            ->name('prescriptions.update')
            ->whereNumber('prescription');

        Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])
            ->name('prescriptions.destroy')
            ->whereNumber('prescription');

        // (اختياري) bulk delete
        Route::delete('/prescriptions/bulk-destroy', [PrescriptionController::class, 'bulkDestroy'])
            ->name('prescriptions.bulkDestroy');
        // مهم: bulk-destroy قبل {prescription} لو هتستخدمها
    });
});
Route::middleware(['auth', 'admin_or_doctor'])->group(function () {

    Route::prefix('patient-transfers')->name('patient-transfers.')->group(function () {

   
        // ===================== CREATE =====================
        Route::get('/create', [PatientTransferController::class, 'create'])
            ->name('create');

        // ===================== STORE =====================
        Route::post('/', [PatientTransferController::class, 'store'])
            ->name('store');

      

        // ===================== EDIT =====================
        Route::get('/{patientTransfer}/edit', [PatientTransferController::class, 'edit'])
            ->name('edit');

        // ===================== UPDATE =====================
        Route::put('/{patientTransfer}', [PatientTransferController::class, 'update'])
            ->name('update');

        // ===================== DELETE =====================
        Route::delete('/{patientTransfer}', [PatientTransferController::class, 'destroy'])
            ->name('destroy');
        Route::delete(
            'patient-transfers/{patientTransfer}/attachments/{attachment}',
            [PatientTransferController::class, 'destroyAttachment']
        )->name('patient-transfers.attachments.destroy');

    });
});
Route::middleware(['auth', 'admin_or_doctor'])->group(function () {
    Route::resource('service-invoices', ServiceInvoiceController::class);
});


  Route::prefix('patient-transfers')->name('patient-transfers.')->group(function () {

     // ===================== LIST =====================
        Route::get('/', [PatientTransferController::class, 'index'])
            ->name('index');

  // ===================== SHOW =====================
        Route::get('/{patientTransfer}', [PatientTransferController::class, 'show'])
            ->name('show');
   });