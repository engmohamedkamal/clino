<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
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
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'dob' => ['required', 'date'],
            'Specialization' => ['required', 'string', 'max:255'],
            'license_number' => [
                'required',
                'numeric',
                'digits_between:5,15',
                Rule::unique('doctors', 'license_number')
                    ->ignore($this->route('doctor')),
            ],
            'address' => ['required', 'string', 'max:2555'],
            'about' => ['required', 'string', 'max:2555'],
            'availability_schedule' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }
}
