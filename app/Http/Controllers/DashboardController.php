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
        $patientsCount = Patient::count();
        $usersPatientsCount = User::where('role', 'patient')->count();
        $totalPatients = $patientsCount + $usersPatientsCount;

        $totalDoctors      = DoctorInfo::count();
        $todayAppointments = Appointment::whereDate('appointment_date', now()->toDateString())->count();
        $activeServices    = Service::where('status', 1)->count();

        $latestPatientsFromUsers = User::where('role', 'patient')
            ->latest()
            ->take(5)
            ->get();
        $latestPatients = $latestPatientsFromUsers;

        $upcomingAppointments = Appointment::whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(3)
            ->get();

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

        // Yearly - MySQL compatible
        $yStart = Carbon::now()->startOfMonth()->subMonths(11);

        $yRows = Appointment::selectRaw("DATE_FORMAT(appointment_date, '%Y-%m') as ym, COUNT(*) as c")
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