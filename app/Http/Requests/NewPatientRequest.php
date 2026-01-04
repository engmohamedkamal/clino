<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class NewPatientRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true; // لو عندك صلاحيات عدّلها
    }

    public function rules(): array
    {
        return [
            'patient_name'   => ['required', 'string', 'min:3', 'max:100'],
            'patient_email'  => ['required', 'email:rfc,dns', 'max:150', 'unique:patients,patient_email'],
            'dob'            => ['required', 'date', 'before:today'],
            'patient_number' => ['required', 'string', 'min:7', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]+$/', 'unique:patients,patient_number'],
            'gender'         => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'id_number'      => ['required', 'string', 'min:6', 'max:30', 'unique:patients,id_number'],
            'address'        => ['required', 'string', 'min:5', 'max:255'],
            'about'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_number.regex' => 'Patient Number must contain only digits and valid symbols like + - ( ) spaces.',
            'dob.before'           => 'Date of birth must be before today.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'patient_name'   => $this->patient_name ? trim($this->patient_name) : null,
            'patient_email'  => $this->patient_email ? strtolower(trim($this->patient_email)) : null,
            'patient_number' => $this->patient_number ? trim($this->patient_number) : null,
            'id_number'      => $this->id_number ? trim($this->id_number) : null,
            'address'        => $this->address ? trim($this->address) : null,
            'about'          => $this->about ? trim($this->about) : null,
        ]);
    }
}
