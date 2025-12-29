<?php

namespace App\Http\Controllers\Patient;

use App\Models\patientInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PatientInfoController extends Controller
{
    public function index()
    {
        // تقدر تضيف شرط role == admin هنا
        $patients = patientInfo::with('user')->latest()->paginate(10);

        return view('patient-info.index', compact('patients'));
    }

    /**
     * عرض فورم إنشاء patient info جديد.
     */
    public function create()
    {
        // مثال: لو كل يوزر له record واحد بس، امنعه لو عنده واحد بالفعل
        if (Auth::user()->patientInfo) {
            return redirect()
                ->route('patient-info.edit', Auth::user()->patientInfo->id)
                ->with('info', 'You already have patient info, you can edit it.');
        }

        return view('patient-info.create');
    }

    /**
     * تخزين بيانات patient info في الداتا بيز.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        // ربط record بالمستخدم الحالي
        $validated['user_id'] = Auth::id();

        // لو عندك قيود إن كل يوزر له record واحد:
        if (PatientInfo::where('user_id', Auth::id())->exists()) {
            return redirect()
                ->route('patient-info.edit', Auth::user()->patientInfo->id)
                ->with('info', 'You already have patient info, you can edit it.');
        }

        $patientInfo = PatientInfo::create($validated);

        return redirect()
            ->route('patient-info.show', $patientInfo->id)
            ->with('success', 'Patient info created successfully');
    }

    /**
     * عرض patient info واحد.
     */
    public function show(PatientInfo $patientInfo)
    {
        // تقدر تضيف Authorization (owner/admin)
        return view('patient-info.show', compact('patientInfo'));
    }

    /**
     * فورم التعديل.
     */
    public function edit(PatientInfo $patientInfo)
    {
        // مثال بسيط: السماح للمالك فقط أو للأدمن
        if (Auth::id() !== $patientInfo->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('patient-info.edit', compact('patientInfo'));
    }

    /**
     * تحديث البيانات.
     */
    public function update(Request $request, PatientInfo $patientInfo)
    {
        if (Auth::id() !== $patientInfo->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'gender' => 'nullable|in:male,female',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:10',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $patientInfo->update($validated);

        return redirect()
            ->route('patient-info.show', $patientInfo->id)
            ->with('success', 'Patient info updated successfully');
    }

    /**
     * حذف record.
     */
    public function destroy(PatientInfo $patientInfo)
    {
        if (Auth::id() !== $patientInfo->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $patientInfo->delete();

        return redirect()
            ->route('patient-info.index')
            ->with('success', 'Patient info deleted successfully');
    }

    /**
     * عرض/تعديل بيانات المريض الحالي فقط (shortcut).
     */
    public function myInfo()
    {
        $patientInfo = Auth::user()->patientInfo;

        if (!$patientInfo) {
            return redirect()
                ->route('patient-info.create')
                ->with('info', 'Please add your patient info first.');
        }

        return view('patient-info.show', compact('patientInfo'));
    }
}
