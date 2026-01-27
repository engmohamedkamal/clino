<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Requests\SettingRequest;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return view('admin.settings.create', compact('settings'));
    }
    public function store(SettingRequest $request)
    {
        $existing = Setting::first();
        if ($existing) {
            return redirect()
                ->route('settings.index')
                ->with('info', 'Settings already exist. You can update them.');
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        Setting::create($data);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings saved successfully.');
    }
    public function update(SettingRequest $request, Setting $setting)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
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
}