<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'gender' => 'nullable|in:male,female',
            'dob' => 'nullable|date',
            'license_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'about' => 'nullable|string',

            // ✅ Multi-valued (JSON arrays)
            'availability_schedule' => 'nullable|array',
            'availability_schedule.*' => 'nullable|string|max:100',

            'Specialization' => 'nullable|array',
            'Specialization.*' => 'nullable|string|max:100',

            'activities' => 'nullable|array',
            'activities.*' => 'nullable|string|max:100',

            // skills: array of objects [{name:"", value:0..100}]
            'skills' => 'nullable|array',
            'skills.*.name' => 'required_with:skills|string|max:100',
            'skills.*.value' => 'required_with:skills|integer|min:0|max:100',

            // social (لو عندك)
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'skills.*.name.required_with' => 'Each skill must have a name.',
            'skills.*.value.required_with' => 'Each skill must have a percentage.',
            'skills.*.value.integer' => 'Skill percentage must be a number.',
            'skills.*.value.max' => 'Skill percentage must be 100 or less.',
        ];
    }
}
