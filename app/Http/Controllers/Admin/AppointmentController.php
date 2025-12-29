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
        $doctors = User::where('role', 'doctor')->get();
        return view('dashboard.appointment.index', compact('doctors'));
    }

    public function store(AppointmentRequest $request)
    {
        $user = Auth::user();
        if ($user->role !== 'patient') {
            Appointment::create([
                'patient_name' => $request->patient_name,
                'doctor_name' => $request->doctor_name,
                'gender' => $request->gender,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'patient_number' => $request->patient_number,
                'dob' => $request->dob,
                'reason' => $request->reason,
            ]);

            return redirect()->route('appointment.show')
                ->with('success', 'Appointment created successfully');
        }
        $patientInfo = patientInfo::where('user_id', $user->id)->first();
        if (!$patientInfo) {
            return back()
                ->withErrors(['patient_info' => 'Please complete your patient info first.'])
                ->withInput();
        }
        Appointment::create([
            'patient_name' => $user->name,
            'patient_number' => $user->phone ?? $request->patient_number, // لو رقمك في users
            'dob' => $patientInfo->dob,
            'gender' => $patientInfo->gender, // لازم تكون male/female
            'doctor_name' => $request->doctor_name,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'reason' => $request->reason,
        ]);

        return redirect()->route('appointment.show')
            ->with('success', 'Appointment created successfully');
    }

    public function show()
    {
        $appointments = Appointment::latest()->paginate(10);
        return view('admin.appointments.show', compact('appointments'));
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->back()->with('success', 'Appointment deleted successfully');
    }

    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $doctors = User::where('role', 'doctor')->get();
        return view('admin.appointments.edit', compact('appointment', 'doctors'));
    }

    public function update(AppointmentRequest $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'patient_name' => $request->patient_name,
            'doctor_name' => $request->doctor_name,
            'gender' => $request->gender,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'patient_number' => $request->patient_number,
            'dob' => $request->dob,
            'reason' => $request->reason,
        ]);

        return redirect()->route('appointment.show')->with('success', 'Appointment updated successfully');
    }
}