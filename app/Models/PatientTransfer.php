<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientTransfer extends Model
{
    protected $fillable = [
        'patient_name',
        'primary_physician_id',

        // لو هتستخدم الاسم Text
        'receiving_doctor_name',
        'receiving_phone',

        'transfer_code',
        'transfer_priority',
        'age',
        'gender',
        'blood_type',
        'current_location',

        'reason_for_transfer',
        'stability_status',
        'primary_diagnosis',
        'medical_summary',

        'transport_mode',
        'continuous_oxygen',
        'cardiac_monitoring',

        'destination_hospital',
        'destination_dept_unit',
        'destination_bed_no',

        'bed_status',
        'status',
        'submitted_at',

        // ✅ attachments text array (json)
        'attachments',
    ];

    protected $casts = [
        'continuous_oxygen'  => 'boolean',
        'cardiac_monitoring' => 'boolean',
        'submitted_at'       => 'datetime',
        'attachments'        => 'array',
    ];

    public function primaryPhysician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_physician_id');
    }

        public function doctorInfo()
{
    return $this->hasOne(DoctorInfo::class, 'user_id');
}
}
