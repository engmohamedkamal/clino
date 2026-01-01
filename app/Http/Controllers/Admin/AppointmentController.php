<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Appointment;
use App\Models\patientInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AppointmentRequest;

class AppointmentController extends Controller
{
    public function index()
    {
        $doctors = User::where('role', 'doctor')->get(['id', 'name']);
        return view('dashboard.appointment.index', compact('doctors'));
    }

 
    public function store(AppointmentRequest $request)
    {
        $user = Auth::user();

        // ✅ Admin / Doctor / ... (not patient)
        if ($user->role !== 'patient') {
            Appointment::create([
                'patient_name' => $request->patient_name,
                'patient_number' => $request->patient_number,
                'dob' => $request->dob,
                'gender' => $request->gender,

                'doctor_name' => $request->doctor_name,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'reason' => $request->reason,
            ]);

            return redirect()->route('appointment.show')
                ->with('success', 'Appointment created successfully');
        }

        // ✅ Patient
        $patientInfo = patientInfo::where('user_id', $user->id)->first();

        if (!$patientInfo) {
            return back()
                ->withErrors(['patient_info' => 'Please complete your patient info first.'])
                ->withInput();
        }

        Appointment::create([
            'patient_name' => $user->name,
            'patient_number' => $user->phone ?? $request->patient_number,
            'dob' => $patientInfo->dob,
            'gender' => $patientInfo->gender,

            'doctor_name' => $request->doctor_name,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'reason' => $request->reason,
        ]);

        return redirect()->route('appointment.show')
            ->with('success', 'Appointment created successfully');
    }


    public function show(Request $request)
    {
        $user = Auth::user();
        $q = $request->get('q');

        $appointments = Appointment::query()
            // Doctor filter
            ->when($user->role === 'doctor', function ($query) use ($user) {
                $query->where('doctor_name', $user->name);
            })
            // Patient filter
            ->when($user->role === 'patient', function ($query) use ($user) {
                $query->where(function ($qq) use ($user) {
                    $qq->where('patient_name', $user->name);

                    if (!empty($user->phone)) {
                        $qq->orWhere('patient_number', $user->phone);
                    }
                });
            })
            // Search
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('patient_name', 'like', "%{$q}%")
                        ->orWhere('patient_number', 'like', "%{$q}%")
                        ->orWhere('doctor_name', 'like', "%{$q}%")
                        ->orWhere('appointment_date', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.appointment.show', compact('appointments'));
    }

  
    public function edit($id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient') {
            abort(403);
        }

        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name) {
            abort(403);
        }

        $doctors = User::where('role', 'doctor')->get(['id', 'name']);
        return view('dashboard.appointment.edit', compact('appointment', 'doctors'));
    }

   
    public function update(AppointmentRequest $request, $id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient') {
            abort(403);
        }

        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name) {
            abort(403);
        }

        $appointment->update([
            'patient_name' => $request->patient_name,
            'patient_number' => $request->patient_number,
            'dob' => $request->dob,
            'gender' => $request->gender,

            'doctor_name' => $request->doctor_name,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'reason' => $request->reason,
        ]);

        return redirect()->route('appointment.show')
            ->with('success', 'Appointment updated successfully');
    }

    /**
     * Delete Appointment (Single)
     * - Admin: allowed
     * - Doctor: only if appointment belongs to him
     * - Patient: forbidden
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient') {
            abort(403);
        }

        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name) {
            abort(403);
        }

        $appointment->delete();

        return redirect()->back()
            ->with('success', 'Appointment deleted successfully');
    }


    public function bulkDestroy(Request $request)
    {
    
        $user = Auth::user();

        if ($user->role === 'patient') {
            abort(403);
        }

        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) === 0) {
            return redirect()->back()->withErrors(['ids' => 'Select at least one appointment to delete.']);
        }

        $query = Appointment::whereIn('id', $ids);
        if ($user->role === 'doctor') {
            $query->where('doctor_name', $user->name);
        }

        $deletedCount = $query->delete();

        if ($deletedCount === 0) {
            return redirect()->back()->withErrors(['ids' => 'No appointments were deleted (not allowed or not found).']);
        }

        return redirect()->back()->with('success', "Deleted {$deletedCount} appointment(s) successfully.");
    }
}
