<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use App\Models\Patient;
use App\Models\Diagnosis;
use App\Models\DoctorInfo;
use App\Models\Appointment;
use App\Models\patientInfo;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Models\PatientTransfer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AppointmentRequest;

class AppointmentController extends Controller
{
    public function index()
    {
        $doctors = User::where('role', 'doctor')->get(['id', 'name']);
        $patients = User::where('role', 'patient')->get(['id', 'name']);
        return view('dashboard.appointment.index', compact('doctors', 'patients'));
    }

    public function store(AppointmentRequest $request)
    {
        $user = Auth::user();

        // ✅ doctor by name (لأن الفورم بيبعت doctor_name = اسم)
        $doctor = User::query()
            ->where('role', 'doctor')
            ->where('name', $request->doctor_name)
            ->firstOrFail();

    
        $visitTypes = [];
        if ($request->filled('visit_type')) {
            $visitTypes[] = [
                'type' => $request->visit_type,
                'price' => (float) $request->visit_price,
            ];
        }

        // ✅ Admin/Doctor
        if ($user->role !== 'patient') {

            Appointment::create([
                'patient_name' => $request->patient_name,
                'patient_number' => $request->patient_number,
                'dob' => $request->dob,
                'gender' => $request->gender,

                'doctor_name' => $doctor->name,

                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,

                // ✅ NEW
                'visit_types' => $visitTypes,

                'reason' => $request->reason,
                'status' => 'pending',
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
            'patient_name' => $user->name,
            'patient_number' => $user->phone,
            'dob' => $patientInfo->dob,
            'gender' => $patientInfo->gender,

            'doctor_name' => $doctor->name,

            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,

            // ✅ NEW
            'visit_types' => $visitTypes,

            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('appointment.show')
            ->with('success', 'Appointment created successfully');
    }

    public function show(Request $request)
    {
        $user = Auth::user();

        $q = trim((string) $request->get('q', ''));
        $day = $request->get('day');
        $status = $request->get('status', 'pending');

        // view mode (table | cards)
        $viewMode = $request->get('view', 'table');

        $allowedStatus = ['pending', 'completed', 'cancelled', 'all'];
        if (!in_array($status, $allowedStatus, true)) {
            $status = 'pending';
        }

    
        $baseQuery = Appointment::query()
            ->when($user && $user->role === 'doctor', function ($q) use ($user) {
                $q->where('doctor_name', $user->name);
            })

            // Patient
            ->when($user && $user->role === 'patient', function ($q) use ($user) {
                $q->where(function ($qq) use ($user) {
                    $qq->where('patient_name', $user->name);
                    if (!empty($user->phone)) {
                        $qq->orWhere('patient_number', $user->phone);
                    }
                });
            })

            // Day
            ->when($day, fn($q) => $q->whereDate('appointment_date', $day))

            // Search
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('patient_name', 'like', "%{$q}%")
                        ->orWhere('patient_number', 'like', "%{$q}%")
                        ->orWhere('doctor_name', 'like', "%{$q}%");
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Counters (من غير status filter)
        |--------------------------------------------------------------------------
        */
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $completedCount = (clone $baseQuery)->where('status', 'completed')->count();
        $cancelledCount = (clone $baseQuery)->where('status', 'cancelled')->count();

   
        $appointments = (clone $baseQuery)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(10)
            ->appends($request->query());

        /*
        |--------------------------------------------------------------------------
        | View selection
        |--------------------------------------------------------------------------
        */
        $view = $viewMode === 'cards'
            ? 'dashboard.appointment.cards'
            : 'dashboard.appointment.show';

        return view($view, compact(
            'appointments',
            'status',
            'q',
            'day',
            'viewMode',
            'pendingCount',
            'completedCount',
            'cancelledCount'
        ));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient')
            abort(403);
        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name)
            abort(403);

        $doctors = User::where('role', 'doctor')->get(['id', 'name']);
        $patients = User::where('role', 'patient')->get(['id', 'name']);
        return view('dashboard.appointment.edit', compact('appointment', 'doctors','patients'));
    }

    public function update(AppointmentRequest $request, $id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient')
            abort(403);
        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name)
            abort(403);

        $visitTypes = [];
        if ($request->filled('visit_type')) {
            $visitTypes[] = [
                'type' => $request->visit_type,
                'price' => (float) $request->visit_price,
            ];
        }

        $appointment->update([
            'patient_name' => $request->patient_name,
            'patient_number' => $request->patient_number,
            'dob' => $request->dob,
            'gender' => $request->gender,

            'doctor_name' => $request->doctor_name,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,

            // ✅ NEW
            'visit_types' => $visitTypes,

            'reason' => $request->reason,
            // 'status'            => $request->status,
        ]);

        return redirect()->route('appointment.show')
            ->with('success', 'Appointment updated successfully');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);

        if ($user->role === 'patient')
            abort(403);
        if ($user->role === 'doctor' && $appointment->doctor_name !== $user->name)
            abort(403);

        $appointment->delete();

        return redirect()->back()
            ->with('success', 'Appointment deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'patient')
            abort(403);

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
        if (!in_array(auth()->user()->role, ['admin', 'doctor']))
            abort(403);

        $request->validate([
            'status' => 'required|in:pending,cancelled,completed',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status updated successfully');
    }

    public function doctorAvailability(User $doctor)
    {
        $info = DoctorInfo::where('user_id', $doctor->id)->first();

        $schedule = $info?->availability_schedule ?? [];
        if (!is_array($schedule))
            $schedule = [];

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
            if (!is_array($row))
                continue;

            $day = $row['day'] ?? null;
            $from = $row['from'] ?? null;
            $to = $row['to'] ?? null;

            if (!$day || !$from || !$to)
                continue;

            $day = ucfirst(strtolower(trim(substr($day, 0, 3)))); // Mon/Sun
            if (!isset($weekdayMap[$day]))
                continue;

            try {
                $start = Carbon::createFromFormat('H:i', $from)->format('H:i');
                $end = Carbon::createFromFormat('H:i', $to)->format('H:i');
            } catch (\Throwable $e) {
                continue;
            }

            if ($start >= $end)
                continue;

            $weekdayIso = $weekdayMap[$day]; // ✅ 1..7
            $timeRangesByWeekday[$weekdayIso][] = [$start, $end];
        }

        if (empty($timeRangesByWeekday)) {
            return ['dates' => []];
        }

        $fromDate = Carbon::today()->format('Y-m-d');
        $toDate = Carbon::today()->addDays($daysAhead)->format('Y-m-d');

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

            if (!isset($timeRangesByWeekday[$weekdayIso]))
                continue;

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

            if (!count($available))
                continue;

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
        $appointment = Appointment::findOrFail($id);

        $patient = User::query()
            ->where('name', $appointment->patient_name)
            ->where('phone', $appointment->patient_number)
            ->first();

        $patientId = $patient?->id;
        $reports = $patientId
            ? Report::where('patient_user_id', $patientId)
                ->latest()
                ->select(['id', 'exam_type', 'exam_date', 'created_at'])
                ->paginate(3, ['*'], 'reports_page')
                ->withQueryString()
            : collect();

        $prescriptions = $patientId
            ? Prescription::where('patient_id', $patientId)
                ->latest()
                ->select(['id', 'created_at'])
                ->paginate(3, ['*'], 'rx_page')
                ->withQueryString()
            : collect();

        $diagnoses = Diagnosis::query()
            ->where('patient_name', $appointment->patient_name)
            ->latest()
            ->select(['id', 'created_at'])
            ->paginate(3, ['*'], 'dx_page')
            ->withQueryString();

        $transfers = PatientTransfer::query()
            ->where('patient_name', $appointment->patient_name)
            ->latest()
            ->select(['id', 'transfer_code', 'created_at'])
            ->paginate(3, ['*'], 'tr_page')
            ->withQueryString();

        $nextAppointment = Appointment::query()
            ->where('status', 'pending')
            ->where('doctor_name', $appointment->doctor_name)
            ->whereDate('appointment_date', $appointment->appointment_date)
            ->where('appointment_time', '>', $appointment->appointment_time)
            ->orderBy('appointment_time')
            ->first();

        return view('dashboard.appointment.details', compact(
            'appointment',
            'patient',
            'reports',
            'prescriptions',
            'diagnoses',
            'transfers',
            'nextAppointment'
        ));
    }



    public function reset(Appointment $appointment)
    {

        $queueNo = request('no');

        return view('dashboard.appointment.reset', compact('appointment', 'queueNo'));
    }

    public function vipPrint(Appointment $appointment)
    {
        return view('dashboard.appointment.vip', compact('appointment'));
    }

    public function daySummary(Request $request)
    {
        $user = Auth::user();

        // ✅ date filter (default today)
        $date = $request->query('date');
        $date = $date ? Carbon::parse($date, 'Africa/Cairo')->toDateString()
            : Carbon::now('Africa/Cairo')->toDateString();

        // ✅ doctor_name filter (string column)
        $doctorName = null;

        if ($user->role === 'doctor') {
            $doctorName = trim((string) ($user->name ?? ''));
            if ($doctorName === '')
                abort(403);
        } elseif ($user->role === 'admin') {
            $doctorName = $request->query('doctor_name');
            $doctorName = is_string($doctorName) ? trim($doctorName) : null;
            if ($doctorName === '')
                $doctorName = null;
        } else {
            abort(403);
        }

        $dateColumn = 'appointment_date';

        // ✅ appointments completed on selected date
        $appointments = Appointment::query()
            ->whereDate($dateColumn, $date)
            ->where('status', 'completed')
            ->when(!empty($doctorName), fn($q) => $q->where('doctor_name', $doctorName))
            ->get(['id', 'patient_name', 'doctor_name', 'appointment_time', 'visit_types']);

        $totalCompleted = $appointments->count();

        // ✅ summary by visit_types[].type
        $summary = [];
        foreach ($appointments as $ap) {
            $vts = $ap->visit_types;

            if (is_string($vts))
                $vts = json_decode($vts, true) ?: [];
            if (!is_array($vts))
                $vts = [];

            foreach ($vts as $item) {
                $type = isset($item['type']) ? trim((string) $item['type']) : '';
                $price = isset($item['price']) ? (float) $item['price'] : 0.0;
                if ($type === '')
                    continue;

                if (!isset($summary[$type])) {
                    $summary[$type] = [
                        'visit_type' => $type,
                        'items_count' => 0,
                        'total_price' => 0.0,
                    ];
                }

                $summary[$type]['items_count'] += 1;
                $summary[$type]['total_price'] += $price;
            }
        }

        $byVisitType = array_values($summary);
        usort($byVisitType, fn($a, $b) => $b['items_count'] <=> $a['items_count']);

        $grandTotalPrice = array_sum(array_map(fn($x) => (float) $x['total_price'], $byVisitType));

        // ✅ (Admin فقط) قائمة الدكاترة الموجودة في appointments لاستخدامها في الفلتر
        $doctorNames = [];
        if ($user->role === 'admin') {
            $doctorNames = Appointment::query()
                ->select('doctor_name')
                ->whereNotNull('doctor_name')
                ->distinct()
                ->orderBy('doctor_name')
                ->pluck('doctor_name')
                ->toArray();
        }

        return view('dashboard.appointment.summary', [
            'date' => $date,
            'doctorName' => $doctorName,
            'doctorNames' => $doctorNames,

            'totalCompleted' => $totalCompleted,
            'grandTotalPrice' => $grandTotalPrice,
            'byVisitType' => $byVisitType,
            'appointments' => $appointments,
        ]);
    }
public function cards(Request $request)
{
    $user = auth()->user();

    $q      = trim((string) $request->get('q', ''));
    $day    = $request->get('day');
    $status = $request->get('status', 'pending');

    $allowedStatus = ['pending', 'completed', 'cancelled', 'all'];
    if (!in_array($status, $allowedStatus, true)) {
        $status = 'pending';
    }

    /*
    |--------------------------------------------------------------------------
    | Base Query (نفس الفلاتر — بدون status)
    |--------------------------------------------------------------------------
    */
    $baseQuery = Appointment::query()

        // ✅ لو دكتور: يعرض مواعيده فقط (عدّلها لو عندك doctor_id بدل الاسم)
        ->when($user && $user->role === 'doctor', function ($q) use ($user) {
            $q->where('doctor_name', $user->name);
        })

        // ✅ لو Patient: يعرض مواعيده فقط (عدّلها لو عندك patient_id بدل الاسم/الموبايل)
        ->when($user && $user->role === 'patient', function ($q) use ($user) {
            $q->where(function ($qq) use ($user) {
                $qq->where('patient_name', $user->name);
                if (!empty($user->phone)) {
                    $qq->orWhere('patient_number', $user->phone);
                }
            });
        })

        // Search
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('patient_name', 'like', "%{$q}%")
                   ->orWhere('doctor_name', 'like', "%{$q}%")
                   ->orWhere('patient_number', 'like', "%{$q}%");
            });
        })

        // Day
        ->when($day, fn ($query) => $query->whereDate('appointment_date', $day));

    /*
    |--------------------------------------------------------------------------
    | Counters (بنفس الفلاتر — بدون status filter)
    |--------------------------------------------------------------------------
    */
    $pendingCount   = (clone $baseQuery)->where('status', 'pending')->count();
    $completedCount = (clone $baseQuery)->where('status', 'completed')->count();
    $cancelledCount = (clone $baseQuery)->where('status', 'cancelled')->count();

    /*
    |--------------------------------------------------------------------------
    | Appointments (مع status)
    |--------------------------------------------------------------------------
    */
    $appointments = (clone $baseQuery)
        ->when($status !== 'all', fn ($q2) => $q2->where('status', $status))
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->paginate(12)
        ->withQueryString();

    return view('dashboard.appointment.cards', compact(
        'appointments',
        'status',
        'q',
        'day',
        'pendingCount',
        'completedCount',
        'cancelledCount'
    ));
}



}
