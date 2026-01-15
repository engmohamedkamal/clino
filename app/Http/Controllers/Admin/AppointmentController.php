<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Appointment;
use App\Models\patientInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AppointmentRequest;
use Carbon\Carbon;
use App\Models\DoctorInfo;

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

        // ✅ doctor by name (لأن الفورم بيبعت doctor_name = اسم)
        $doctor = User::query()
            ->where('role', 'doctor')
            ->where('name', $request->doctor_name)
            ->firstOrFail();

        // =========================
        // ✅ Visit Type (JSON)
        // expected from form: visit_type + visit_price
        // store in DB column: visit_types (json)
        // =========================
        $visitTypes = [];
        if ($request->filled('visit_type')) {
            $visitTypes[] = [
                'type'  => $request->visit_type,
                'price' => (float) $request->visit_price,
            ];
        }

        // ✅ Admin/Doctor
        if ($user->role !== 'patient') {

            Appointment::create([
                'patient_name'      => $request->patient_name,
                'patient_number'    => $request->patient_number,
                'dob'               => $request->dob,
                'gender'            => $request->gender,

                'doctor_name'       => $doctor->name,

                'appointment_date'  => $request->appointment_date,
                'appointment_time'  => $request->appointment_time,

                // ✅ NEW
                'visit_types'       => $visitTypes,

                'reason'            => $request->reason,
                'status'            => 'pending',
            ]);

            return redirect()->route('appointment.show')
                ->with('success', 'Appointment created successfully');
        }

        // ✅ Patient
        $patientInfo = patientInfo::where('user_id', $user->id)->first();

        $missingInfo =
            !$patientInfo ||
            empty($user->name) ||
            empty($user->phone) ||
            empty($patientInfo->dob) ||
            empty($patientInfo->gender);

        if ($missingInfo) {
            return redirect()->route('patient-info.create')
                ->with('error', 'Complete your profile first, then rebook your appointment');
        }

        Appointment::create([
            'patient_name'      => $user->name,
            'patient_number'    => $user->phone,
            'dob'               => $patientInfo->dob,
            'gender'            => $patientInfo->gender,

            'doctor_name'       => $doctor->name,

            'appointment_date'  => $request->appointment_date,
            'appointment_time'  => $request->appointment_time,

            // ✅ NEW
            'visit_types'       => $visitTypes,

            'reason'            => $request->reason,
            'status'            => 'pending',
        ]);

        return redirect()->route('appointment.show')
            ->with('success', 'Appointment created successfully');
    }

    public function show(Request $request)
    {
        $user = Auth::user();

        $q   = $request->get('q');
        $day = $request->get('day'); // expected: YYYY-MM-DD

        $appointments = Appointment::query()

            // ===================== Role Filtering =====================
            ->when($user && $user->role === 'doctor', function ($query) use ($user) {
                $query->where('doctor_name', $user->name);
            })

            ->when($user && $user->role === 'patient', function ($query) use ($user) {
                $query->where(function ($qq) use ($user) {
                    $qq->where('patient_name', $user->name);

                    if (!empty($user->phone)) {
                        $qq->orWhere('patient_number', $user->phone);
                    }
                });
            })

            // ===================== Day Filter =====================
            ->when($day, function ($query) use ($day) {
                $query->whereDate('appointment_date', $day);
            })

            // ===================== Search =====================
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('patient_name', 'like', "%{$q}%")
                        ->orWhere('patient_number', 'like', "%{$q}%")
                        ->orWhere('doctor_name', 'like', "%{$q}%")
                        ->orWhere('appointment_date', 'like', "%{$q}%");
                });
            })

            // ===================== Latest + Pagination =====================
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.appointment.show', compact('appointments'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient') abort(403);
        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name) abort(403);

        $doctors = User::where('role', 'doctor')->get(['id', 'name']);
        return view('dashboard.appointment.edit', compact('appointment', 'doctors'));
    }

    public function update(AppointmentRequest $request, $id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient') abort(403);
        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name) abort(403);

        // =========================
        // ✅ Visit Type (JSON)
        // =========================
        $visitTypes = [];
        if ($request->filled('visit_type')) {
            $visitTypes[] = [
                'type'  => $request->visit_type,
                'price' => (float) $request->visit_price,
            ];
        }

        $appointment->update([
            'patient_name'      => $request->patient_name,
            'patient_number'    => $request->patient_number,
            'dob'               => $request->dob,
            'gender'            => $request->gender,

            'doctor_name'       => $request->doctor_name,
            'appointment_date'  => $request->appointment_date,
            'appointment_time'  => $request->appointment_time,

            // ✅ NEW
            'visit_types'       => $visitTypes,

            'reason'            => $request->reason,
            // 'status'            => $request->status,
        ]);

        return redirect()->route('appointment.show')
            ->with('success', 'Appointment updated successfully');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient') abort(403);
        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name) abort(403);

        $appointment->delete();

        return redirect()->back()
            ->with('success', 'Appointment deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'patient') abort(403);

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

    public function updateStatus(Request $request, Appointment $appointment)
    {
        if (!in_array(auth()->user()->role, ['admin', 'doctor'])) abort(403);

        $request->validate([
            'status' => 'required|in:pending,cancelled,completed',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status updated successfully');
    }

    /**
     * ✅ API: يرجّع dates[] + times[] لكل تاريخ
     * schedule stored as: [{day:"Mon", from:"13:00", to:"17:00"}, ...]
     * slots: 15 min
     * excludes booked (except cancelled)
     */
    public function doctorAvailability(User $doctor)
    {
        $info = DoctorInfo::where('user_id', $doctor->id)->first();

        $schedule = $info?->availability_schedule ?? [];
        if (!is_array($schedule)) $schedule = [];

        $payload = $this->buildAvailabilityByDate(
            $schedule,
            $doctor->name,
            14,
            15
        );

        return response()->json($payload);
    }

    private function buildAvailabilityByDate(array $schedule, string $doctorName, int $daysAhead = 14, int $slotMinutes = 15): array
    {
        // ✅ ISO map (Mon=1 .. Sun=7)
        $weekdayMap = [
            'Mon' => 1,
            'Tue' => 2,
            'Wed' => 3,
            'Thu' => 4,
            'Fri' => 5,
            'Sat' => 6,
            'Sun' => 7,
        ];

        $timeRangesByWeekday = []; // [weekdayIso => [[start,end], ...]]

        foreach ($schedule as $row) {
            if (!is_array($row)) continue;

            $day  = $row['day'] ?? null;
            $from = $row['from'] ?? null;
            $to   = $row['to'] ?? null;

            if (!$day || !$from || !$to) continue;

            $day = ucfirst(strtolower(trim(substr($day, 0, 3)))); // Mon/Sun
            if (!isset($weekdayMap[$day])) continue;

            try {
                $start = Carbon::createFromFormat('H:i', $from)->format('H:i');
                $end   = Carbon::createFromFormat('H:i', $to)->format('H:i');
            } catch (\Throwable $e) {
                continue;
            }

            if ($start >= $end) continue;

            $weekdayIso = $weekdayMap[$day]; // ✅ 1..7
            $timeRangesByWeekday[$weekdayIso][] = [$start, $end];
        }

        if (empty($timeRangesByWeekday)) {
            return ['dates' => []];
        }

        $fromDate = Carbon::today()->format('Y-m-d');
        $toDate   = Carbon::today()->addDays($daysAhead)->format('Y-m-d');

        $booked = Appointment::query()
            ->where('doctor_name', $doctorName)
            ->whereBetween('appointment_date', [$fromDate, $toDate])
            ->where('status', '!=', 'cancelled')
            ->get(['appointment_date', 'appointment_time'])
            ->groupBy('appointment_date')
            ->map(function ($rows) {
                return $rows->pluck('appointment_time')
                    ->map(function ($t) {
                        try {
                            return Carbon::parse($t)->format('H:i');
                        } catch (\Throwable $e) {
                            return (string) $t;
                        }
                    })
                    ->unique()
                    ->values()
                    ->all();
            })
            ->all();

        $dates = [];
        $today = Carbon::today();

        for ($i = 0; $i <= $daysAhead; $i++) {
            $d = $today->copy()->addDays($i);
            $weekdayIso = $d->dayOfWeekIso; // ✅ 1..7

            if (!isset($timeRangesByWeekday[$weekdayIso])) continue;

            $slots = [];
            foreach ($timeRangesByWeekday[$weekdayIso] as [$start, $end]) {
                $t = Carbon::createFromFormat('H:i', $start);
                $endT = Carbon::createFromFormat('H:i', $end);

                while ($t->lt($endT)) {
                    $slots[] = $t->format('H:i');
                    $t->addMinutes($slotMinutes);
                }
            }

            $slots = array_values(array_unique($slots));

            $dateValue = $d->format('Y-m-d');
            $bookedTimes = $booked[$dateValue] ?? [];
            $available = array_values(array_diff($slots, $bookedTimes));

            if (!count($available)) continue;

            $dates[] = [
                'value' => $dateValue,
                'label' => $d->format('D d M Y'),
                'times' => $available,
            ];
        }

        return ['dates' => $dates];
    }

    public function singleShow($id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        return view('dashboard.appointment.details', compact('appointment'));
    }
}
