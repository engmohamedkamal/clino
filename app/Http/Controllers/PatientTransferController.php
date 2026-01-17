<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientTransferRequest;
use App\Models\PatientTransfer;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientTransferController extends Controller
{
    /**
     * Display a listing of transfers.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $transfers = PatientTransfer::query()
            ->with([
                'primaryPhysician:id,name',
            ])
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

    /**
     * Show the form for creating a new transfer.
     */
    public function create(Request $request)
    {
        // Optional: /patient-transfers/create?patient_id=1
        $patientId = $request->get('patient_id');

        $patient = null;
        if ($patientId) {
            $patient = Patient::findOrFail($patientId);
        }

        $primaryPhysician = auth()->user();

        // قائمة الدكاترة (لـ primary_physician_id فقط)
        $doctors = User::query()
            ->whereIn('role', ['doctor', 'admin'])
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $transfer_code = 'TR-' . now()->format('Ymd') . '-' . random_int(1000, 9999);

        return view('dashboard.transfer.create', compact('patient', 'doctors', 'primaryPhysician', 'transfer_code'));
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
