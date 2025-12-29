<?php

namespace App\Http\Controllers;

use App\Models\UserInfo;
use App\Models\patientInfo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserInfoController extends Controller
{
    public function myInfo()
    {
        
        $info = Auth::user()->patientInfo; 

        if (!$info) {
            return redirect()->route('patient-info.create')
                ->with('info', 'Please add your info first.');
        }

        return view('patient-info.show', compact('info'));
    }
    public function create()
    {
        $info = Auth::user()->patientInfo;

        if ($info) {
            return redirect()->route('patient-info.edit', $info->id)
                ->with('info', 'You already added your info. You can update it.');
        }

        return view('patient-info.create');
    }

    /**
     * ✅ Store
     */
    public function store(Request $request)
    {
        
        // ممنوع يعمل create لو عنده info
        if (Auth::user()->patientInfo) {
            return redirect()->route('patient-info.my')
                ->with('info', 'You already added your info. You can update it.');
        }

        $data = $this->validated($request, null);
        $data['user_id'] = Auth::id();

        $info = PatientInfo::create($data);

        return redirect()->route('patient-info.my')
            ->with('success', 'Info saved successfully ✅');
    }

    /**
     * ✅ Show (resource) - اختياري تسيبه شغال
     */
    public function show(PatientInfo $patient_info)
    {
        abort_if($patient_info->user_id !== Auth::id(), 403);

        $info = $patient_info;
        return view('patient-info.show', compact('info'));
    }

    /**
     * ✅ Edit (resource)
     */
    public function edit(patientInfo $patient_info)
    {
        // dd(Auth::user()->patientInfo);

        abort_if($patient_info->user_id !== Auth::id(), 403);

        $info = $patient_info;
        return view('patient-info.edit', compact('info'));
    }

    /**
     * ✅ Update (resource)
     */
    public function update(Request $request, PatientInfo $patient_info)
    {
        abort_if($patient_info->user_id !== Auth::id(), 403);

        $data = $this->validated($request, $patient_info->id);

        $patient_info->update($data);

        return redirect()->route('patient-info.my')
            ->with('success', 'Info updated successfully ✅');
    }

    /**
     * ✅ Validation
     */
    private function validated(Request $request, ?int $ignoreId): array
    {
        return $request->validate([
            'gender' => 'required|in:male,female',
            'dob' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'blood_type' => 'nullable|string|max:10',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
    }
}

