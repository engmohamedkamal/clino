<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Requests\NewPatientRequest;

class PatientController extends Controller
{
public function index(Request $request)
{
    $q = $request->query('q');

    // ✅ Query 1: search in patients table
    $patients = Patient::query()
        ->when($q, function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('patient_name', 'like', "%{$q}%")
                    ->orWhere('patient_number', 'like', "%{$q}%")
                    ->orWhere('patient_email', 'like', "%{$q}%")
                    ->orWhere('id_number', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();
    $users = User::query()
        ->where('role', 'patient')
        ->when($q, function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%"); // لو عندك phone
            });
        })
        ->latest()
        ->paginate(10, ['*'], 'users_page') // ✅ عشان pagination ما تلخبطش مع patients
        ->withQueryString();

    return view('dashboard.patient.view', compact('patients', 'users', 'q'));
}
public function cards(Request $request)
{
    $q = $request->query('q');

    // ✅ Query 1: search in patients table
    $patients = Patient::query()
        ->when($q, function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('patient_name', 'like', "%{$q}%")
                    ->orWhere('patient_number', 'like', "%{$q}%")
                    ->orWhere('patient_email', 'like', "%{$q}%")
                    ->orWhere('id_number', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    // ✅ Query 2: search in users table (role=patient)
    $users = User::query()
        ->where('role', 'patient')
        ->when($q, function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        })
        ->latest()
        ->paginate(10, ['*'], 'users_page')
        ->withQueryString();

    // ✅ رجّع صفحة الكروت
    return view('dashboard.patient.card', compact('patients', 'users', 'q'));
}


    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->withErrors('Select at least one patient.');
        }
        Patient::whereIn('id', $ids)->delete();
        return redirect()->route('patients.index')->with('success', 'Selected patients deleted ✅');
    }

    public function create()
    {
        return view('dashboard.patient.add');
    }

    public function store(NewPatientRequest $request)
    {
        Patient::create($request->validated());

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient created successfully ✅');
    }


    public function edit(Patient $patient)
    {
        return view('dashboard.patient.edit', compact('patient'));
    }

    public function update(NewPatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient updated successfully ✅');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deleted successfully ✅');
    }
}
