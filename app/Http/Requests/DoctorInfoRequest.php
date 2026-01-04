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
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH'], true);

        return [
            'gender'         => ['required','in:male,female'],
            'dob'            => ['required','date'],
            'license_number' => ['required','string','max:255'],
            'address'        => ['required','string','max:255'],

            // ✅ image: required في create، optional في update
            'image' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:2048'
            ],

            'about' => ['required','string'],

            // ✅ Availability schedule (array of rows)
            'availability_schedule' => ['required','array','min:1'],
            'availability_schedule.*.day'  => ['required','in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'availability_schedule.*.from' => ['required','date_format:H:i'],
            'availability_schedule.*.to'   => ['required','date_format:H:i'],

            // ✅ Multi-valued (JSON arrays)
            'Specialization'   => ['required','array','min:1'],
            'Specialization.*' => ['required','string','max:100'],

            'activities'   => ['required','array','min:1'],
            'activities.*' => ['required','string','max:100'],

            // ✅ skills: array of objects [{name:"", value:0..100}]
            'skills' => ['required','array','min:1'],
            'skills.*.name'  => ['required','string','max:100'],
            'skills.*.value' => ['required','integer','min:0','max:100'],

            // social
            'facebook'  => ['nullable','url','max:255'],
            'instagram' => ['nullable','url','max:255'],
            'twitter'   => ['nullable','url','max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        // ✅ ensure "to" بعد "from" لكل صف
        $validator->after(function ($v) {
            $rows = $this->input('availability_schedule', []);
            if (!is_array($rows)) return;

            foreach ($rows as $i => $row) {
                $from = $row['from'] ?? null;
                $to   = $row['to'] ?? null;

                if (!$from || !$to) continue;

                // string compare on H:i works, but we'll keep it explicit
                if ($to <= $from) {
                    $v->errors()->add("availability_schedule.$i.to", "The end time must be after the start time.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            // Availability
            'availability_schedule.required' => 'Please add at least one availability row.',
            'availability_schedule.array'    => 'Availability schedule must be a list.',
            'availability_schedule.min'      => 'Please add at least one availability row.',

            'availability_schedule.*.day.required' => 'Please select a day.',
            'availability_schedule.*.day.in'       => 'Day must be one of: Mon, Tue, Wed, Thu, Fri, Sat, Sun.',

            'availability_schedule.*.from.required' => 'Start time is required.',
            'availability_schedule.*.from.date_format' => 'Start time must be in HH:MM format.',

            'availability_schedule.*.to.required' => 'End time is required.',
            'availability_schedule.*.to.date_format' => 'End time must be in HH:MM format.',

            // Skills
            'skills.*.name.required'  => 'Each skill must have a name.',
            'skills.*.value.required' => 'Each skill must have a percentage.',
            'skills.*.value.integer'  => 'Skill percentage must be a number.',
            'skills.*.value.max'      => 'Skill percentage must be 100 or less.',

            // Image
            'image.required' => 'Please upload a profile picture.',
        ];
    }
}
