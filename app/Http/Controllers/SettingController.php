<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
       public function index()
    {
        // Record واحد فقط
        $settings = Setting::first();
        return view('admin.settings.create', compact('settings'));
    }

    public function store(Request $request)
    {
        // لو موجود بالفعل → ودّيه update بدل ما يعمل record جديد
        $existing = Setting::first();
        if ($existing) {
            return redirect()
                ->route('settings.index')
                ->with('info', 'Settings already exist. You can update them.');
        }

        $data = $this->validateData($request);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        $setting = Setting::create($data);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings saved successfully.');
    }

    public function update(Request $request, Setting $setting)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('logo')) {
            // delete old
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        $setting->update($data);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate(
            [
                'name'      => 'required|string|max:255',
                'slogan'    => 'required|string|max:255',
                'vision'    => 'required|string',
                'mission'   => 'required|string',

                'facebook'  => 'required|url|max:255',
                'instagram' => 'required|url|max:255',
                'twitter'   => 'required|url|max:255',

                'phone'     => 'required|string|max:20',
                'email'     => 'required|email|max:255',
                'address'   => 'required|string|max:255',

                'logo'      => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            ],
            [
                'name.required'   => 'Name is required.',
                'email.email'     => 'Email must be valid.',
                'facebook.url'    => 'Facebook must be a valid URL.',
                'instagram.url'   => 'Instagram must be a valid URL.',
                'twitter.url'     => 'Twitter must be a valid URL.',
                'logo.image'      => 'Logo must be an image.',
            ]
        );
    }
}
