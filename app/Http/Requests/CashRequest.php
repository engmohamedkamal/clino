<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashRequest extends FormRequest
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
            
        'service'  => ['nullable', 'string', 'max:255'],
        'cash'     => ['nullable', 'numeric', 'min:0'],
        'cash_out' => ['nullable', 'numeric', 'min:0'],
   
        ];
    }

    public function messages(): array
    {
        return [
            'cash.required' => 'Cash amount is required',
            'cash.numeric' => 'Cash must be a number',
            'cash.min' => 'Cash cannot be negative',

            'cash_out.numeric' => 'Cash out must be a number',
            'cash_out.min' => 'Cash out cannot be negative',

            'service.required' => 'Service name is required',
        ];
    }
}
