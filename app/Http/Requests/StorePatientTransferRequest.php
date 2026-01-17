<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        // عدلها لو عندك صلاحيات roles
        return auth()->check();
    }

    public function rules(): array
    {
        return [

          
            // لو بتخزن الاسم في جدول transfers (patient_name column)
            'patient_name' => ['required', 'string', 'max:255'],

            'primary_physician_id' => ['nullable', 'exists:users,id'],

            // receiving doctor text (مش relation)
            'receiving_doctor_name' => ['nullable', 'string', 'max:255'],
            'receiving_phone' => ['nullable', 'string', 'max:20'],

            // ================== Header Info ==================
            'transfer_code' => ['nullable', 'string', 'max:50'],
            'transfer_priority' => ['required', 'in:urgent,normal'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['nullable', 'in:male,female'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'current_location' => ['nullable', 'string', 'max:255'],

            // ================== Clinical Assessment ==================
            'reason_for_transfer' => ['required', 'string', 'max:255'],
            'stability_status' => ['required', 'in:stable,guarded,critical'],
            'primary_diagnosis' => ['nullable', 'string', 'max:255'],
            'medical_summary' => ['nullable', 'string'],

            // ================== Transport & Logistics ==================
            'transport_mode' => ['required', 'in:als_ambulance,wheelchair_van,other'],
            'continuous_oxygen' => ['nullable', 'boolean'],
            'cardiac_monitoring' => ['nullable', 'boolean'],

            // ================== Destination ==================
            'destination_hospital' => ['required', 'string', 'max:255'],
            'destination_dept_unit' => ['nullable', 'string', 'max:255'],
            'destination_bed_no' => ['nullable', 'string', 'max:100'],

            // ================== Workflow ==================
            'bed_status' => ['nullable', 'in:pending,confirmed,denied'],
            'status' => ['nullable', 'in:draft,submitted,in_transit,completed,cancelled'],

            // ================== Attachments ==================
         'attachments' => ['nullable', 'array'],
'attachments.*' => ['nullable', 'string', 'max:255'],

        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',

            'patient_name.required' => 'Patient name is required.',

            'reason_for_transfer.required' => 'Reason for transfer is required.',
            'transport_mode.required' => 'Transport mode must be selected.',
            'destination_hospital.required' => 'Destination hospital is required.',

            'attachments.*.mimes' => 'Only PDF, JPG, PNG, WEBP, GIF, or DICOM files are allowed.',
            'attachments.*.max' => 'File size must not exceed 50MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            // checkboxes: لو مش موجودة في request تبقى false
            'continuous_oxygen' => $this->boolean('continuous_oxygen'),
            'cardiac_monitoring' => $this->boolean('cardiac_monitoring'),

            // defaults (لو UI ما بعتش حاجة)
            'bed_status' => $this->input('bed_status', 'pending'),
            'status' => $this->input('status', 'submitted'),
        ]);
    }
}
