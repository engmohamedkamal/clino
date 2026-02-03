<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            'doctor_name' => ['required', 'string', Rule::exists('users', 'name')->where('role', 'doctor')],

            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required'],
            'reason' => ['nullable', 'string'],
            'emergency' => ['nullable', 'boolean'],
            'vip' => ['nullable', 'boolean'],

        ];

        // ✅ لو مش patient (Admin/Doctor) يبقى لازم يدخل بيانات المريض من الفورم
        if ($user && $user->role !== 'patient') {
            $rules['patient_name'] = ['required', 'string', 'max:255'];
            $rules['patient_number'] = ['required', 'string', 'max:50'];
            $rules['dob'] = ['required', 'date'];
            $rules['gender'] = ['required', 'in:male,female,Other'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'doctor_name.required' => 'Please select a doctor.',
            'appointment_date.required' => 'Please choose a date.',
            'appointment_time.required' => 'Please choose a time.',
        ];
    }

}
