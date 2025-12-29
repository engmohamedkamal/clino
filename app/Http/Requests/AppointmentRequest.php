<?php

namespace App\Http\Requests;

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

        // rules common for all
        $rules = [
            'doctor_name' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:1000',
        ];

        // Admin/Doctor لازم يدخل بيانات المريض
        if ($user && $user->role !== 'patient') {
            $rules += [
                'patient_name' => 'required|string|max:255',
                'patient_number' => 'required|string|max:20',
                'dob' => 'required|date',
                'gender' => 'required|in:male,female',
            ];
        }

        // Patient مش محتاج يبعتهُم (هنا Optional أو نسيبهم خالص)
        // لو عندك input hidden ممكن تخليهم nullable
        else {
            $rules += [
                'patient_name' => 'nullable',
                'patient_number' => 'nullable',
                'dob' => 'nullable',
                'gender' => 'nullable',
            ];
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
