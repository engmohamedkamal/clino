<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // either existing patient id OR "__new__"
            'patient_id' => ['required', 'string', function ($attribute, $value, $fail) {
                if ($value === '__new__') return;

                if (!is_numeric($value)) {
                    return $fail('Please select a valid patient.');
                }
            }],

            // required only when patient_id="__new__"
            'patient_name_new' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn () => $this->input('patient_id') === '__new__')],

            'public_diagnosis'  => ['required', 'string', 'max:255'],
            'private_diagnosis' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // نظّف الاسم الجديد
        if ($this->has('patient_name_new')) {
            $this->merge([
                'patient_name_new' => trim((string)$this->input('patient_name_new')),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'patient_name_new.required' => 'New patient name is required.',
            'public_diagnosis.required' => 'Public diagnosis is required.',
        ];
    }
}
