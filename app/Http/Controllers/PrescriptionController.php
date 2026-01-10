<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorInfo;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /* ================= Helpers ================= */

    private function userRole(): string
    {
        return (string) (auth()->user()->role ?? '');
    }

    private function isAdmin(): bool
    {
        return $this->userRole() === 'admin';
    }

    private function isDoctor(): bool
    {
        return $this->userRole() === 'doctor';
    }

    private function isPatient(): bool
    {
        return $this->userRole() === 'patient';
    }

    private function canManage(): bool
    {
        return $this->isAdmin() || $this->isDoctor();
    }

    private function currentDoctorId(): ?int
    {
        // ✅ عدّل اسم العلاقة لو عندك مختلفة
        return auth()->user()->doctorInfo->id ?? null;
    }

    private function scopeByRole($query)
    {
        // ✅ Patient: يشوف روشتته فقط (patient_id = users.id)
        if ($this->isPatient()) {
            return $query->where('patient_id', auth()->id());
        }

        // ✅ Doctor: يشوف اللي هو كاتبه فقط
        if ($this->isDoctor()) {
            $did = $this->currentDoctorId();
            return $query->when($did, fn ($q) => $q->where('doctor_id', $did))
                         ->when(!$did, fn ($q) => $q->whereRaw('1=0'));
        }

        // ✅ Admin: الكل
        return $query;
    }

    private function findAllowedOrFail(int $id): Prescription
    {
        return $this->scopeByRole(
            Prescription::with(['patientUser', 'doctor.user'])
        )->findOrFail($id);
    }

    /* ================= Index ================= */

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $baseQuery = Prescription::with(['patientUser', 'doctor.user'])->latest();

        $prescriptions = $this->scopeByRole($baseQuery)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('medicine_name', 'like', "%{$q}%")
                      ->orWhere('dosage', 'like', "%{$q}%")
                      ->orWhere('duration', 'like', "%{$q}%")
                      ->orWhere('diagnosis', 'like', "%{$q}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        $canManage = $this->canManage();

        return view('dashboard.prescriptions.index', compact('prescriptions', 'canManage', 'q'));
    }

    /* ================= Show ================= */

  public function show($id)
{
    $rx = Prescription::with(['patientUser', 'doctor.user'])->findOrFail($id);
    return view('dashboard.prescriptions.show', compact('rx'));
}


    /* ================= Create ================= */

    public function create()
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $doctorId = $this->currentDoctorId();

        // ✅ المرضى = users where role = patient
        $patients = User::where('role', 'patient')->latest()->get();

        // ✅ Admin فقط يختار doctor_id
        $doctors = $this->isAdmin()
            ? DoctorInfo::with('user')->latest()->get()
            : collect();

        return view('dashboard.prescriptions.create', compact('patients', 'doctors', 'doctorId'));
    }

    /* ================= Store ================= */

    public function store(Request $request)
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $rules = [
            'patient_id'    => ['required', 'integer', 'exists:users,id'],
            'medicine_name' => ['required', 'string', 'max:255'],
            'dosage'        => ['required', 'string', 'max:255'],
            'duration'      => ['required', 'string', 'max:255'],
            'diagnosis'     => ['required', 'string', 'max:255'],
            'notes'         => ['nullable', 'string'],
        ];

        if ($this->isAdmin()) {
            $rules['doctor_id'] = ['required', 'integer', 'exists:doctor_infos,id'];
        }

        $data = $request->validate($rules);

        // ✅ تأكد إن المختار فعلاً patient
        $patientUser = User::where('role', 'patient')->find($data['patient_id']);
        if (!$patientUser) {
            return back()->withErrors(['patient_id' => 'Selected user is not a patient.'])->withInput();
        }

        // ✅ Doctor: doctor_id تلقائي
        if ($this->isDoctor()) {
            $doctorId = $this->currentDoctorId();
            if (!$doctorId) {
                return back()->withErrors(['doctor_id' => 'Doctor profile not found.'])->withInput();
            }
            $data['doctor_id'] = $doctorId;
        }

        $rx = Prescription::create($data);

        return redirect()->route('prescriptions.show', $rx->id)
            ->with('success', 'Prescription created successfully.');
    }

    /* ================= Edit ================= */

    public function edit($prescription)
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $rx = $this->findAllowedOrFail((int) $prescription);

        $patients = User::where('role', 'patient')->latest()->get();

        $doctors = $this->isAdmin()
            ? DoctorInfo::with('user')->latest()->get()
            : collect();

        return view('dashboard.prescriptions.edit', compact('rx', 'patients', 'doctors'));
    }

    /* ================= Update ================= */

    public function update(Request $request, $prescription)
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $rx = $this->findAllowedOrFail((int) $prescription);

        $rules = [
            'patient_id'    => ['required', 'integer', 'exists:users,id'],
            'medicine_name' => ['required', 'string', 'max:255'],
            'dosage'        => ['required', 'string', 'max:255'],
            'duration'      => ['required', 'string', 'max:255'],
            'diagnosis'     => ['required', 'string', 'max:255'],
            'notes'         => ['nullable', 'string'],
        ];

        if ($this->isAdmin()) {
            $rules['doctor_id'] = ['required', 'integer', 'exists:doctor_infos,id'];
        }

        $data = $request->validate($rules);

        // ✅ تأكد إن patient_id المختار فعلاً role=patient
        $patientUser = User::where('role', 'patient')->find($data['patient_id']);
        if (!$patientUser) {
            return back()->withErrors(['patient_id' => 'Selected user is not a patient.'])->withInput();
        }

        // ✅ Doctor: امنع تغيير doctor_id
        if ($this->isDoctor()) {
            $data['doctor_id'] = $rx->doctor_id;
        }

        $rx->update($data);

        return redirect()->route('prescriptions.show', $rx->id)
            ->with('success', 'Prescription updated successfully.');
    }

    /* ================= Destroy ================= */

    public function destroy($prescription)
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $rx = $this->findAllowedOrFail((int) $prescription);
        $rx->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription deleted successfully.');
    }

    /* ================= Bulk Destroy (Optional) ================= */

    public function bulkDestroy(Request $request)
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $data = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $deleted = $this->scopeByRole(Prescription::query())
            ->whereIn('id', $data['ids'])
            ->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', "Deleted {$deleted} prescription(s).");
    }
}
