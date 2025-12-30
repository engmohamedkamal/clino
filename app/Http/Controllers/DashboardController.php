<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Service;
use App\Models\DoctorInfo;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
public function index()
{
    // Stats
    $totalPatients     = Patient::count();
    $totalDoctors      = DoctorInfo::count();
    $todayAppointments = Appointment::whereDate('appointment_date', now()->toDateString())->count();
    $activeServices    = Service::where('status', 1)->count();

    // Latest Patients
    $latestPatients = Patient::latest()->take(5)->get();

    // Upcoming
    $upcomingAppointments = Appointment::whereDate('appointment_date', '>=', now()->toDateString())
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->take(5)
        ->get();

    // =========================
    // Charts data (Weekly/Monthly/Yearly)
    // =========================

    // 1) Weekly (last 7 days) - daily grouping
    $wStart = Carbon::today()->subDays(6);
    $wEnd   = Carbon::today();

    $wRows = Appointment::query()
        ->selectRaw('DATE(appointment_date) as d, COUNT(*) as c')
        ->whereDate('appointment_date', '>=', $wStart->toDateString())
        ->whereDate('appointment_date', '<=', $wEnd->toDateString())
        ->groupBy('d')
        ->orderBy('d')
        ->pluck('c', 'd')
        ->toArray();

    $weeklyLabels = [];
    $weeklyData   = [];
    for ($i = 0; $i < 7; $i++) {
        $day = $wStart->copy()->addDays($i);
        $key = $day->toDateString();
        $weeklyLabels[] = $day->format('M d');
        $weeklyData[]   = (int) ($wRows[$key] ?? 0);
    }

    // 2) Monthly (last 30 days) - daily grouping
    $mStart = Carbon::today()->subDays(29);
    $mEnd   = Carbon::today();

    $mRows = Appointment::query()
        ->selectRaw('DATE(appointment_date) as d, COUNT(*) as c')
        ->whereDate('appointment_date', '>=', $mStart->toDateString())
        ->whereDate('appointment_date', '<=', $mEnd->toDateString())
        ->groupBy('d')
        ->orderBy('d')
        ->pluck('c', 'd')
        ->toArray();

    $monthlyLabels = [];
    $monthlyData   = [];
    for ($i = 0; $i < 30; $i++) {
        $day = $mStart->copy()->addDays($i);
        $key = $day->toDateString();
        $monthlyLabels[] = $day->format('M d');
        $monthlyData[]   = (int) ($mRows[$key] ?? 0);
    }

    // 3) Yearly (last 12 months) - monthly grouping
    $yStart = Carbon::now()->startOfMonth()->subMonths(11);
    $yEnd   = Carbon::now()->endOfMonth();

    // key => YYYY-MM
    $yRows = Appointment::query()
        ->selectRaw("DATE_FORMAT(appointment_date, '%Y-%m') as ym, COUNT(*) as c")
        ->whereDate('appointment_date', '>=', $yStart->toDateString())
        ->whereDate('appointment_date', '<=', $yEnd->toDateString())
        ->groupBy('ym')
        ->orderBy('ym')
        ->pluck('c', 'ym')
        ->toArray();

    $yearlyLabels = [];
    $yearlyData   = [];
    for ($i = 0; $i < 12; $i++) {
        $month = $yStart->copy()->addMonths($i);
        $key = $month->format('Y-m');
        $yearlyLabels[] = $month->format('M Y');
        $yearlyData[]   = (int) ($yRows[$key] ?? 0);
    }

    // Default view = Monthly
    $chartLabels = $monthlyLabels;
    $chartData   = $monthlyData;

    return view('dashboard.index', compact(
        'totalPatients',
        'totalDoctors',
        'todayAppointments',
        'activeServices',
        'latestPatients',
        'upcomingAppointments',

        // default chart
        'chartLabels',
        'chartData',

        // datasets for dropdown
        'weeklyLabels',
        'weeklyData',
        'monthlyLabels',
        'monthlyData',
        'yearlyLabels',
        'yearlyData'
    ));
}
}
