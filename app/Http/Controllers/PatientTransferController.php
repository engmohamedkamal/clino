<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientTransferRequest;
use App\Models\PatientTransfer;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientTransferController extends Controller
{

    public function index(Request $request)
{
    $user = auth()->user();
    $role = $user->role ?? '';

    $q = trim((string) $request->get('q', ''));

    $transfers = PatientTransfer::query()
        ->with([
            'primaryPhysician:id,name',
            // لو عندك relation للمريض ضيفيها هنا:
            // 'patient:id,patient_name',
            // 'patientUser:id,name',
        ])

        // ✅ Role-based filtering
        ->when($role === 'doctor', function ($query) use ($user) {
            // الدكتور يشوف التحويلات اللي هو primary physician بتاعها
            $query->where('primary_physician_id', $user->id);
        })
        ->when($role === 'patient', function ($query) use ($user) {
            // المريض يشوف تحويلاته فقط
            // (1) لو عندك patient_user_id
            if (\Illuminate\Support\Facades\Schema::hasColumn('patient_transfers', 'patient_name')) {
                $query->where('patient_name', $user->name);
                return;
            }

            // (2) لو عندك patient_id و patients فيها user_id
            if (
                \Illuminate\Support\Facades\Schema::hasColumn('patient_transfers', 'patient_id')
                && class_exists(\App\Models\Patient::class)
                && \Illuminate\Support\Facades\Schema::hasColumn('patients', 'user_id')
            ) {
                $patientId = \App\Models\Patient::where('user_id', $user->id)->value('id');
                $query->where('patient_id', $patientId ?? 0);
                return;
            }

            // (3) fallback (لو مفيش ربط واضح) امنعي العرض
            $query->whereRaw('1=0');
        })

        // ✅ Search
        ->when($q, function ($query) use ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('transfer_code', 'like', "%{$q}%")
                    ->orWhere('destination_hospital', 'like', "%{$q}%")
                    ->orWhere('receiving_doctor_name', 'like', "%{$q}%")
                    ->orWhere('patient_name', 'like', "%{$q}%");
            });
        })

        ->latest()
        ->paginate(5)
        ->withQueryString();

    return view('dashboard.transfer.index', compact('transfers', 'q'));
}


  
public function create(Request $request)
{
    // /patient-transfers/create?patient_id=1&appointment_id=5
    $selectedPatientId = $request->query('patient_id');
    $appointmentId     = $request->query('appointment_id');
    $patientsFromPatientsTable = Patient::query()
        ->selectRaw('id, patient_name as name')
        ->get();

    $patientsFromUsersTable = User::query()
        ->where('role', 'patient')
        ->selectRaw('id, name')
        ->get();
$patients = User::where('role', 'patient')->latest()->get();
        $patient = $request->patient_name;

    if ($selectedPatientId) {
        $exists =
            Patient::whereKey($selectedPatientId)->exists()
            || User::where('role', 'patient')->whereKey($selectedPatientId)->exists();

        abort_unless($exists, 404);
    }

    $primaryPhysician = auth()->user();

    $doctors = User::query()
        ->whereIn('role', ['doctor', 'admin'])
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

    $transfer_code = 'TR-' . now()->format('Ymd') . '-' . random_int(1000, 9999);

    return view('dashboard.transfer.create', compact(
        'patients','patient',
        'selectedPatientId',
        'appointmentId',
        'doctors',
        'primaryPhysician',
        'transfer_code'
    ));
}


    /**
     * Store a newly created transfer in storage.
     */
    public function store(StorePatientTransferRequest $request)
    {
        $data = $request->validated();

        // defaults (لو مش جايين)
        $data['bed_status'] = $data['bed_status'] ?? 'pending';
        $data['status']     = $data['status'] ?? 'submitted';

        // ✅ attachments text array: اضمن إنه array حتى لو null
        $data['attachments'] = is_array($data['attachments'] ?? null) ? $data['attachments'] : [];

        $transfer = PatientTransfer::create($data);
$user = auth()->user();


        return redirect()
            ->route('patient-transfers.show', $transfer->id)
            ->with('success', 'Transfer created successfully.');
    }

    /**
     * Display the specified transfer.
     */
    public function show(PatientTransfer $patientTransfer)
    {
        $transfer = $patientTransfer->load([
            'primaryPhysician:id,name',
        ]);

        return view('dashboard.transfer.show', compact('transfer'));
    }

    /**
     * Show the form for editing the specified transfer.
     */
    public function edit(PatientTransfer $patientTransfer)
    {
        $patientTransfer->load(['primaryPhysician:id,name']);

        // لو بتستخدم patient_id query في create فقط، هنا هنمشي بالاسم الموجود
        // ولو حابب تعرض بيانات patient من جدول patients لازم يكون عندك patient_id (مش موجود عندك حاليًا)
        $patient = null;

        $primaryPhysician = $patientTransfer->primaryPhysician ?? auth()->user();

        $doctors = User::query()
            ->whereIn('role', ['doctor', 'admin'])
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('dashboard.transfer.edit', compact('patientTransfer', 'patient', 'doctors', 'primaryPhysician'));
    }

    /**
     * Update the specified transfer in storage.
     */
    public function update(StorePatientTransferRequest $request, PatientTransfer $patientTransfer)
    {
        $data = $request->validated();

        $data['attachments'] = is_array($data['attachments'] ?? null) ? $data['attachments'] : [];

        $patientTransfer->update($data);

        return redirect()
            ->route('patient-transfers.show', $patientTransfer->id)
            ->with('success', 'Transfer updated successfully.');
    }

    /**
     * Remove the specified transfer from storage.
     */
    public function destroy(PatientTransfer $patientTransfer)
    {
        $patientTransfer->delete();

        return redirect()
            ->route('patient-transfers.index')
            ->with('success', 'Transfer deleted successfully.');
    }
}
