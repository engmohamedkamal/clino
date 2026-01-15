<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\User;
use App\Models\Report;
use App\Models\Patient;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    
public function index(Request $request)
{
    $q = $request->query('q');
    $user = auth()->user();

    $reports = Report::query()
        ->with(['patient', 'doctor'])

        // Doctor
        ->when($user->role === 'doctor', function ($query) use ($user) {
            $query->where('doctor_id', $user->id);
        })

        // Patient (فلترة مباشرة على reports)
        ->when($user->role === 'patient', function ($query) use ($user) {
            $query->where('patient_user_id', $user->id);
        })

        // Search (متغلفة عشان OR ما يكسرش الفلترة)
        ->when($q, function ($query) use ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('exam_type', 'like', "%{$q}%")
                   ->orWhereHas('patient', function ($p) use ($q) {
                       $p->where('patient_name', 'like', "%{$q}%")
                         ->orWhere('patient_number', 'like', "%{$q}%");
                   })
                   ->orWhereHas('doctor', function ($d) use ($q) {
                       $d->where('name', 'like', "%{$q}%");
                   });
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('dashboard.reports.index', compact('reports', 'q'));
}

public function create()
{
    $patientsFromPatients = Patient::query()
        ->orderBy('patient_name')
        ->get()
        ->map(fn($p) => (object) [
            'source' => 'patients',
            'id'     => $p->id,
            'name'   => $p->patient_name,
            'phone'  => $p->patient_number ?? null,
        ]);

    $patientsFromUsers = User::query()
        ->where('role', 'patient')
        ->orderBy('name')
        ->get()
        ->map(fn($u) => (object) [
            'source' => 'users',
            'id'     => $u->id,
            'name'   => $u->name,
            'phone'  => $u->phone ?? null,
        ]);

    $patients = $patientsFromPatients
        ->merge($patientsFromUsers)
        ->sortBy('name')
        ->values();

    return view('dashboard.reports.create', [
    'patients'   => $patients,
    'patient_id' => request('patient_id'), // أو اللي جاي من الراوت
]);

}

public function store(Request $request)
{
    $data = $request->validate([
        'patient_ref' => 'required|string',
        'exam_type'   => 'required|string|max:100',
        'exam_date'   => 'required|date',
        'exam_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'note'        => 'nullable|string',
    ]);

    [$source, $id] = explode(':', $data['patient_ref']) + [null, null];

    $data['patient_id'] = null;
    $data['patient_user_id'] = null;

    if ($source === 'patients') {
        Patient::findOrFail($id);
        $data['patient_id'] = (int) $id;
    } elseif ($source === 'users') {
        User::where('id', $id)->where('role', 'patient')->firstOrFail();
        $data['patient_user_id'] = (int) $id;
    } else {
        return back()->withErrors(['patient_ref' => 'Invalid patient selection'])->withInput();
    }

    unset($data['patient_ref']);

    $data['doctor_id'] = auth()->id();

    if ($request->hasFile('exam_image')) {
        $data['exam_image'] = $request->file('exam_image')->store('reports', 'public');
    }

    Report::create($data);

    return redirect()->route('reports.index')->with('success', 'Report created successfully.');
}

    public function show(Report $report)
    {

    
    $report->load(['patient', 'patientUser', 'doctor']);

        $user = auth()->user();

        if ($user->role === 'admin') {
            return view('dashboard.reports.show', compact('report'));
        }

        if ($user->role === 'doctor') {
            abort_if($report->doctor_id !== $user->id, 403, 'Not allowed: This report is not yours.');
            return view('dashboard.reports.show', compact('report'));
        }

        if ($user->role === 'patient') {
            // لازم يكون فيه patient.user_id
            abort_if(is_null(optional($report->patient)->user_id), 403, 'Your patient profile is not linked to your account.');
            abort_if($report->patient->user_id !== $user->id, 403, 'Not allowed: This report is not yours.');
            return view('dashboard.reports.show', compact('report'));
        }

        abort(403);
    }


    // =========================
    // EDIT
    // =========================
    public function edit(Report $report)
    {
         $patientsFromPatients = Patient::query()
        ->orderBy('patient_name')
        ->get()
        ->map(fn($p) => (object) [
            'source' => 'patients',
            'id'     => $p->id,
            'name'   => $p->patient_name,
            'phone'  => $p->patient_number ?? null,
        ]);

    $patientsFromUsers = User::query()
        ->where('role', 'patient')
        ->orderBy('name')
        ->get()
        ->map(fn($u) => (object) [
            'source' => 'users',
            'id'     => $u->id,
            'name'   => $u->name,
            'phone'  => $u->phone ?? null,
        ]);

    $patients = $patientsFromPatients
        ->merge($patientsFromUsers)
        ->sortBy('name')
        ->values();
        return view('dashboard.reports.edit', compact('report', 'patients'));
    }

    // =========================
    // UPDATE
    // =========================
  public function update(Request $request, Report $report)
{
    $data = $request->validate([
        'patient_ref' => 'required|string',
        'exam_type'   => 'required|string|max:100',
        'exam_date'   => 'required|date',
        'exam_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'note'        => 'nullable|string',
    ]);

    [$source, $id] = explode(':', $data['patient_ref']) + [null, null];

    $data['patient_id'] = null;
    $data['patient_user_id'] = null;

    if ($source === 'patients') {
        Patient::findOrFail($id);
        $data['patient_id'] = (int) $id;
    } elseif ($source === 'users') {
        User::where('id', $id)->where('role', 'patient')->firstOrFail();
        $data['patient_user_id'] = (int) $id;
    } else {
        return back()->withErrors(['patient_ref' => 'Invalid patient selection'])->withInput();
    }

    unset($data['patient_ref']);

    // image update
    if ($request->hasFile('exam_image')) {
        if ($report->exam_image && \Storage::disk('public')->exists($report->exam_image)) {
            \Storage::disk('public')->delete($report->exam_image);
        }
        $data['exam_image'] = $request->file('exam_image')->store('reports', 'public');
    }

    $report->update($data);

    return redirect()->route('reports.index', $report->id)->with('success', 'Report updated successfully.');
}


    // =========================
    // DESTROY
    // =========================
    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()
            ->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
