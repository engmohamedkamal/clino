<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $patients = \App\Models\Patient::query()
            ->when($q, function ($query) use ($q) {
                $query->where('patient_name', 'like', "%{$q}%")
                    ->orWhere('patient_number', 'like', "%{$q}%")
                    ->orWhere('patient_email', 'like', "%{$q}%")
                    ->orWhere('id_number', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.patient.view', compact('patients', 'q'));
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
