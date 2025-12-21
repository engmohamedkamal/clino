<?php

namespace App\Http\Controllers;

use App\Models\DoctorInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorInfoController extends Controller
{
    /**
     * Open form only if first time
     */

    public function create()
    {
        $doctorInfo = DoctorInfo::where('user_id', Auth::id())->first();

        if ($doctorInfo) {
            return redirect()->route('doctor-info.edit')
                             ->with('error', 'You have already entered your info.');
        }

        return view('doctor-info.create');
    }

    /**
     * Store doctor info once
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'gender' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'availability_schedule' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'about' => 'nullable|string',
        ]);

        // Check if already exists
        if (DoctorInfo::where('user_id', Auth::id())->exists()) {
            return redirect()->route('doctor-info.edit')
                             ->with('error', 'Info already exists');
        }

        $validated['user_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('doctor_images', 'public');
        }

        DoctorInfo::create($validated);

        return redirect()->route('doctor-info.edit')->with('success', 'Doctor Info Created Successfully.');
    }

    /**
     * Edit form
     */
    public function edit()
    {
        $doctorInfo = DoctorInfo::where('user_id', Auth::id())->first();

        if (!$doctorInfo) {
            return redirect()->route('doctor-info.create')
                             ->with('error', 'You must add your info first.');
        }

        return view('doctor-info.edit', compact('doctorInfo'));
    }

    /**
     * Update data
     */
    public function update(Request $request)
    {
        $doctorInfo = DoctorInfo::where('user_id', Auth::id())->first();

        if (!$doctorInfo) {
            return redirect()->route('doctor-info.create')
                             ->with('error', 'You must add your info first.');
        }

        $validated = $request->validate([
            'gender' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'availability_schedule' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'about' => 'nullable|string',
        ]);

        // Update Image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('doctor_images', 'public');
        }

        $doctorInfo->update($validated);

        return redirect()->route('doctor-info.edit')->with('success', 'Doctor Info Updated Successfully.');
    }
}
