<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ✅ قبل الـ validation: نحول أي رابط Maps (Share/Short) لـ Embed URL صالح للـ iframe
     */
    protected function prepareForValidation(): void
    {
        $url = trim((string) $this->input('map_url'));

        if ($url !== '') {
            $this->merge([
                'map_url' => $this->toEmbedMapUrl($url),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name'      => 'nullable|string|max:255',
            'slogan'    => 'nullable|string|max:255',
            'vision'    => 'nullable|string',
            'mission'   => 'nullable|string',
            'facebook'  => 'nullable|url|max:1024',
            'instagram' => 'nullable|url|max:1024',
            'twitter'   => 'nullable|url|max:1024',
            'logo'      => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'address'   => 'nullable|string|max:1024',
            'map_url'   => 'nullable|url|max:1500',
        ];
    }

    public function messages(): array
    {
        return [
            'facebook.url'  => 'رابط الفيسبوك يجب أن يكون رابط صحيح.',
            'instagram.url' => 'رابط الانستجرام يجب أن يكون رابط صحيح.',
            'twitter.url'   => 'رابط التويتر يجب أن يكون رابط صحيح.',
            'map_url.url'   => 'رابط الموقع يجب أن يكون رابط صحيح.',
            'logo.image'    => 'الملف المرفوع يجب أن يكون صورة.',
            'logo.mimes'    => 'مسموح بالصور من الأنواع: png, jpg, jpeg, svg.',
            'logo.max'      => 'حجم الصورة لا يجب أن يزيد عن 2 ميجا.',
            'email.email'   => 'من فضلك أدخل بريد إلكتروني صحيح.',
        ];
    }

    /**
     * تحويل أي رابط Google Maps إلى Embed URL
     */
    private function toEmbedMapUrl(string $url): string
    {
        // ✅ لو Embed أصلاً
        if (str_contains($url, 'google.com/maps/embed')) {
            return $url;
        }

        // ✅ فك short links: maps.app.goo.gl / goo.gl
        if (str_contains($url, 'maps.app.goo.gl') || str_contains($url, 'goo.gl')) {
            $resolved = $this->resolveRedirectUrl($url);
            if ($resolved) {
                $url = $resolved;
            }
        }

        // ✅ لو فيه @lat,lng داخل الرابط
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $m)) {
            $lat = $m[1];
            $lng = $m[2];
            return "https://www.google.com/maps?q={$lat},{$lng}&z=16&output=embed";
        }

        // ✅ fallback: نخليها embed بـ q= (ممتاز لأي place/link/address)
        return 'https://www.google.com/maps?output=embed&q=' . urlencode($url);
    }

    /**
     * يرجع الـ final URL بعد الـ redirects (مهم للـ short links)
     */
    private function resolveRedirectUrl(string $url): ?string
    {
        try {
            $resp = Http::withOptions([
                'allow_redirects' => true,
                'timeout' => 8,
            ])->get($url);

            $stats = method_exists($resp, 'handlerStats') ? $resp->handlerStats() : [];
            $finalUrl = $stats['url'] ?? null;

            return $finalUrl ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
