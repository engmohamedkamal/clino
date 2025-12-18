<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'name'      => 'nullable|string|max:255',
            'slogan'    => 'nullable|string|max:255',
            'vision'    => 'nullable|string',
            'mission'   => 'nullable|string',
            'facebook'  => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter'   => 'nullable|url|max:255',
            'logo'      => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048', 
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'address'   => 'nullable|string|max:255',
        ];
        
    }
    public function messages(): array
{
    return [
        'facebook.url'  => 'رابط الفيسبوك يجب أن يكون رابط صحيح.',
        'instagram.url' => 'رابط الانستجرام يجب أن يكون رابط صحيح.',
        'twitter.url'   => 'رابط التويتر يجب أن يكون رابط صحيح.',
        'logo.image'    => 'الملف المرفوع يجب أن يكون صورة.',
        'logo.mimes'    => 'مسموح بالصور من الأنواع: png, jpg, jpeg, svg.',
        'logo.max'      => 'حجم الصورة لا يجب أن يزيد عن 2 ميجا.',
        'email.email'   => 'من فضلك أدخل بريد إلكتروني صحيح.',
    ];
}
}