<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreServiceInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // لو عندك middleware admin_or_doctor يكفي
        // لكن لو عايز تأمين إضافي:
        return auth()->check() && in_array(auth()->user()->role ?? '', ['admin', 'doctor']);
    }

    public function rules(): array
    {
        return [
            // ================= Patient =================
            'patient_id'          => ['required'], // رقم أو __new__
            'patient_name_new'    => ['nullable', 'string', 'max:255'],
            'patient_phone_new'   => ['nullable', 'string', 'max:30'],
            'patient_code_new'    => ['nullable', 'string', 'max:50'], // PID-...

            'insurance_provider'  => ['nullable', 'string', 'max:255'],

            // ================= Payment =================
            'payment_method'      => ['required', 'in:card,cash,insurance,wallet'],
            'payment_status'      => ['required', 'in:pending,paid,partial'],

            // ================= Notes =================
            'notes'               => ['nullable', 'string', 'max:5000'],

            // ================= Totals inputs (optional) =================
            // انت ممكن تخليهم يتحسبوا في الكنترولر، بس نخليهم اختيارية لو بتبعتها من الفورم
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'tax_percent'         => ['nullable', 'numeric', 'min:0', 'max:100'],

            // ================= Items =================
            'items'               => ['required', 'array', 'min:1'],

            'items.*.service_id'    => ['nullable', 'integer'], // لو عندك services table هنتأكد بالـ after
            'items.*.service_name'  => ['required', 'string', 'max:255'],

            'items.*.doctor_name'   => ['nullable', 'string', 'max:255'],
            'items.*.department'    => ['nullable', 'string', 'max:255'],

            'items.*.qty'           => ['required', 'integer', 'min:1'],
            'items.*.unit_price'    => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {

            // ================= patient_id logic =================
            $pid = (string) $this->input('patient_id');

            if ($pid === '__new__') {
                $newName = trim((string) $this->input('patient_name_new'));
                if ($newName === '') {
                    $v->errors()->add('patient_name_new', 'New patient name is required.');
                }
                return;
            }

            // لازم يكون رقم
            if (!ctype_digit($pid)) {
                $v->errors()->add('patient_id', 'Selected patient is invalid.');
                return;
            }

            // لازم يكون موجود role=patient
            $exists = \App\Models\User::query()
                ->where('role', 'patient')
                ->where('id', (int)$pid)
                ->exists();

            if (!$exists) {
                $v->errors()->add('patient_id', 'Selected patient not found.');
            }

            // ================= items logic =================
            $items = $this->input('items', []);
            if (!is_array($items) || count($items) === 0) {
                $v->errors()->add('items', 'Please add at least 1 service item.');
                return;
            }

            // لو عندك جدول services وعايز تتحقق من service_id:
            foreach ($items as $i => $it) {
                $serviceId = $it['service_id'] ?? null;

                if ($serviceId !== null && $serviceId !== '') {
                    if (!is_numeric($serviceId)) {
                        $v->errors()->add("items.$i.service_id", 'Service ID must be a number.');
                        continue;
                    }

                    // لو عندك Model Service
                    if (class_exists(\App\Models\Service::class)) {
                        $serviceExists = \App\Models\Service::query()
                            ->where('id', (int)$serviceId)
                            ->exists();

                        if (!$serviceExists) {
                            $v->errors()->add("items.$i.service_id", 'Selected service not found.');
                        }
                    }
                }

                // service_name لازم يكون موجود حتى لو service_id null
                $name = trim((string)($it['service_name'] ?? ''));
                if ($name === '') {
                    $v->errors()->add("items.$i.service_name", 'Service name is required.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'items.required'      => 'Please add at least 1 service.',
            'items.array'         => 'Items must be an array.',
            'items.min'           => 'Please add at least 1 service.',
            'items.*.qty.min'     => 'Quantity must be at least 1.',
            'items.*.unit_price.min' => 'Unit price must be 0 or more.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // تنظيف بسيط للقيم
        $this->merge([
            'patient_id'    => is_string($this->patient_id ?? null) ? trim($this->patient_id) : $this->patient_id,
            'discount'      => $this->discount === '' ? null : $this->discount,
            'tax_percent'   => $this->tax_percent === '' ? null : $this->tax_percent,
        ]);

        // تأكد items هي array
        $items = $this->input('items', []);
        if (!is_array($items)) {
            $this->merge(['items' => []]);
        }
    }
}
