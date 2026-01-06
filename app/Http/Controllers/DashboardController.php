<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Models\Service;
use App\Models\DoctorInfo;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        // =========================
        // Patients counts (2 queries بدون email exception)
        // =========================

        $patientsCount = Patient::count();

        $usersPatientsCount = User::where('role', 'patient')->count();

        $totalPatients = $patientsCount + $usersPatientsCount;

        // =========================
        // Other Stats
        // =========================
        $totalDoctors      = DoctorInfo::count();
        $todayAppointments = Appointment::whereDate('appointment_date', now()->toDateString())->count();
        $activeServices    = Service::where('status', 1)->count();

        // =========================
        // Latest Patients (2 queries + merge)
        // =========================

        $latestPatientsFromPatients = Patient::latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                return (object) [
                    'source' => 'patients',
                    'id'     => $p->id,
                    'name'   => $p->patient_name,
                    'email'  => $p->patient_email,
                    'phone'  => $p->patient_number,
                    'created_at' => $p->created_at,
                    'raw'    => $p,
                ];
            });

        $latestPatientsFromUsers = User::where('role', 'patient')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($u) {
                return (object) [
                    'source' => 'users',
                    'id'     => $u->id,
                    'name'   => $u->name,
                    'email'  => $u->email,
                    'phone'  => $u->phone ?? null,
                    'created_at' => $u->created_at,
                    'raw'    => $u,
                ];
            });

        $latestPatients = $latestPatientsFromPatients
            ->merge($latestPatientsFromUsers)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        // =========================
        // Upcoming Appointments
        // =========================
        $upcomingAppointments = Appointment::whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // =========================
        // Charts data
        // =========================

        // Weekly
        $wStart = Carbon::today()->subDays(6);

        $wRows = Appointment::selectRaw('DATE(appointment_date) d, COUNT(*) c')
            ->whereDate('appointment_date', '>=', $wStart)
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $weeklyLabels = [];
        $weeklyData   = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $wStart->copy()->addDays($i);
            $weeklyLabels[] = $day->format('M d');
            $weeklyData[]   = (int) ($wRows[$day->toDateString()] ?? 0);
        }

        // Monthly
        $mStart = Carbon::today()->subDays(29);

        $mRows = Appointment::selectRaw('DATE(appointment_date) d, COUNT(*) c')
            ->whereDate('appointment_date', '>=', $mStart)
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $monthlyLabels = [];
        $monthlyData   = [];

        for ($i = 0; $i < 30; $i++) {
            $day = $mStart->copy()->addDays($i);
            $monthlyLabels[] = $day->format('M d');
            $monthlyData[]   = (int) ($mRows[$day->toDateString()] ?? 0);
        }

        // Yearly
        $yStart = Carbon::now()->startOfMonth()->subMonths(11);

        $yRows = Appointment::selectRaw("DATE_FORMAT(appointment_date,'%Y-%m') ym, COUNT(*) c")
            ->whereDate('appointment_date', '>=', $yStart)
            ->groupBy('ym')
            ->pluck('c', 'ym')
            ->toArray();

        $yearlyLabels = [];
        $yearlyData   = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $yStart->copy()->addMonths($i);
            $yearlyLabels[] = $month->format('M Y');
            $yearlyData[]   = (int) ($yRows[$month->format('Y-m')] ?? 0);
        }

        // Default chart
        $chartLabels = $monthlyLabels;
        $chartData   = $monthlyData;

        return view('dashboard.index', compact(
            'totalPatients',
            'totalDoctors',
            'todayAppointments',
            'activeServices',
            'latestPatients',
            'upcomingAppointments',
            'chartLabels',
            'chartData',
            'weeklyLabels',
            'weeklyData',
            'monthlyLabels',
            'monthlyData',
            'yearlyLabels',
            'yearlyData'
        ));
    }
}
