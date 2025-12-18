<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        if (!$setting) {
            return redirect()->route('settings.create');
        }
        return redirect()->route('settings.edit');
    }

    public function create()
    {
        if (Setting::exists()) {
            return redirect()->route('settings.edit')
                ->with('error', 'Settings already exist, you can only edit them.');
        }

        return view('admin.settings.create');
    }

    public function store(SettingRequest $request)
    {
        if (Setting::exists()) {
            return redirect()->route('settings.index')
                ->with('error', 'Settings already exist.');
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->logo->store('logos', 'public');
        }

        Setting::create($data);

        return redirect()->route('settings.edit')->with('success', 'Settings created successfully');
    }

    // صفحة التعديل
    public function edit()
    {
        $setting = Setting::firstOrFail();

        return view('admin.settings.edit', compact('setting'));
    }

    // تحديث الإعدادات
    public function update(SettingRequest $request)
    {
        $setting = Setting::firstOrFail();

        $data = $request->validated();

        // لو فيه لوجو جديد
        if ($request->hasFile('logo')) {
            // امسح القديم لو موجود
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }

            $data['logo'] = $request->logo->store('logos', 'public');
        } else {
            // لو مفيش صورة جديدة → خليه زي ما هو
            $data['logo'] = $setting->logo;
        }

        $setting->update($data);

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully');
    }

    // هنقفل الديستروي عشان مفيش حذف للإعدادات
    public function destroy()
    {
        abort(404);
    }
}
