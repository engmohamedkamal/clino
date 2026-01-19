<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorInfo;
use App\Models\Prescription;
use App\Models\Medicine as MedicalOrder; // ✅ جدول medical orders (name,type)
use Illuminate\Http\Request;
use App\Models\PatientInfo;
class PrescriptionController extends Controller
{
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
        return auth()->user()->doctorInfo->id ?? null;
    }

    private function scopeByRole($query)
    {
        if ($this->isPatient()) {
            return $query->where('patient_id', auth()->id());
        }

        if ($this->isDoctor()) {
            $did = $this->currentDoctorId();

            return $query->when($did, fn($q) => $q->where('doctor_id', $did))
                ->when(!$did, fn($q) => $q->whereRaw('1=0'));
        }

        return $query;
    }

    private function findAllowedOrFail(int $id): Prescription
    {
        return $this->scopeByRole(
            Prescription::with(['patientUser', 'doctor.user'])
        )->findOrFail($id);
    }

    /**
     * ✅ helper: يرجّع lists من جدول medical orders حسب النوع
     * (بنستخدمها في create/edit عشان الـ selects)
     */
    private function getOrdersLists(): array
    {
        $medicinesList = MedicalOrder::where('type', 'medicine')->orderBy('name')->get();
        $rumorsList = MedicalOrder::where('type', 'rumor')->orderBy('name')->get();
        $analysesList = MedicalOrder::where('type', 'analysis')->orderBy('name')->get();

        return compact('medicinesList', 'rumorsList', 'analysesList');
    }

    /* ================= Index ================= */

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $baseQuery = Prescription::with(['patientUser', 'doctor.user'])->latest();

        $prescriptions = $this->scopeByRole($baseQuery)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    // ملاحظة: الحقول json ممكن تنبحث كـ string لأن cast->array بيترجع json
                    $w->where('medicine_name', 'like', "%{$q}%")
                        ->orWhere('dosage', 'like', "%{$q}%")
                        ->orWhere('duration', 'like', "%{$q}%")
                        ->orWhere('rumor', 'like', "%{$q}%")
                        ->orWhere('analysis', 'like', "%{$q}%")
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
        $rx = $this->findAllowedOrFail((int) $id);

        // ================= Patient URL =================
        $patientId = $rx->patient_id ?? null;

        $patientInfo = $patientId
            ? PatientInfo::where('user_id', $patientId)->first()
            : null;

        $patientUrl = $patientInfo
            ? route('patient-info.show', $patientInfo->id)
            : '#';

        // ================= Doctor URL =================
        $doctorId = $rx->doctor_id ?? null;

        $doctorUrl = $doctorId
            ? route('doctor-info.show', $doctorId)
            : '#';

        return view(
            'dashboard.prescriptions.show',
            compact('rx', 'patientUrl', 'doctorUrl')
        );
    }


    /* ================= Create ================= */

    public function create()
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $doctorId = $this->currentDoctorId();

        $patients = User::where('role', 'patient')->latest()->get();

        $doctors = $this->isAdmin()
            ? DoctorInfo::with('user')->latest()->get()
            : collect();

        // ✅ NEW: القوائم من جدول medical orders
        $lists = $this->getOrdersLists();

        return view('dashboard.prescriptions.create', array_merge(
            compact('patients', 'doctors', 'doctorId'),
            $lists
        ));
    }

    /* ================= Store ================= */

    public function store(Request $request)
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $rules = [
            'patient_id' => ['required', 'integer', 'exists:users,id'],

            // ✅ Medicines (required)
            'medicine_name' => ['required', 'array', 'min:1'],
            'medicine_name.*' => ['required', 'string', 'max:255'],

            'dosage' => ['required', 'array', 'min:1'],
            'dosage.*' => ['required', 'string', 'max:255'],

            'duration' => ['required', 'array', 'min:1'],
            'duration.*' => ['required', 'string', 'max:255'],

            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string'],

            // ✅ NEW: Radiology + Analysis (optional)
            'rumor_name' => ['nullable', 'array'],
            'rumor_name.*' => ['nullable', 'string', 'max:255'],

            'analysis_name' => ['nullable', 'array'],
            'analysis_name.*' => ['nullable', 'string', 'max:255'],

            'diagnosis' => ['required', 'string', 'max:255'],
        ];

        if ($this->isAdmin()) {
            $rules['doctor_id'] = ['required', 'integer', 'exists:doctor_infos,id'];
        }

        $data = $request->validate($rules);

        // ✅ تأكد إن المختار patient فعلاً
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

        // ✅ تنظيف medicines rows
        $medicine = collect($data['medicine_name'])->map(fn($v) => trim((string) $v))->filter()->values()->all();
        $dosage = collect($data['dosage'])->map(fn($v) => trim((string) $v))->filter()->values()->all();
        $duration = collect($data['duration'])->map(fn($v) => trim((string) $v))->filter()->values()->all();

        $notes = collect($data['notes'] ?? [])
            ->map(fn($v) => is_null($v) ? null : trim((string) $v))
            ->values()
            ->all();

        $count = count($medicine);
        if ($count === 0 || count($dosage) !== $count || count($duration) !== $count) {
            return back()
                ->withErrors(['medicine_name' => 'Please fill medicine, dosage and duration for each row.'])
                ->withInput();
        }

        if (count($notes) < $count) {
            $notes = array_pad($notes, $count, null);
        }

        $data['medicine_name'] = $medicine;
        $data['dosage'] = $dosage;
        $data['duration'] = $duration;
        $data['notes'] = $notes;

        // ✅ NEW: rumor + analysis (json arrays)
        $data['rumor'] = collect($data['rumor_name'] ?? [])
            ->map(fn($v) => trim((string) $v))
            ->filter()
            ->values()
            ->all();

        $data['analysis'] = collect($data['analysis_name'] ?? [])
            ->map(fn($v) => trim((string) $v))
            ->filter()
            ->values()
            ->all();

        unset($data['rumor_name'], $data['analysis_name']);

        $rx = Prescription::create($data);
        $user = auth()->user();

        if ($user->role === 'doctor') {
            return session('return_to')
                ? redirect(session('return_to'))->with('success', 'Report created successfully.')
                : redirect()->back()->with('success', 'Report created successfully.');
        }
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

        // ✅ NEW: القوائم من جدول medical orders
        $lists = $this->getOrdersLists();

        return view('dashboard.prescriptions.edit', array_merge(
            compact('rx', 'patients', 'doctors'),
            $lists
        ));
    }

    /* ================= Update ================= */

    public function update(Request $request, $prescription)
    {
        if (!$this->canManage()) {
            abort(403);
        }

        $rx = $this->findAllowedOrFail((int) $prescription);

        // ✅ مهم: هنا كان عندك غلط (string بدل array) — اتصلح
        $rules = [
            'patient_id' => ['required', 'integer', 'exists:users,id'],

            'medicine_name' => ['required', 'array', 'min:1'],
            'medicine_name.*' => ['required', 'string', 'max:255'],

            'dosage' => ['required', 'array', 'min:1'],
            'dosage.*' => ['required', 'string', 'max:255'],

            'duration' => ['required', 'array', 'min:1'],
            'duration.*' => ['required', 'string', 'max:255'],

            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string'],

            // ✅ NEW
            'rumor_name' => ['nullable', 'array'],
            'rumor_name.*' => ['nullable', 'string', 'max:255'],

            'analysis_name' => ['nullable', 'array'],
            'analysis_name.*' => ['nullable', 'string', 'max:255'],

            'diagnosis' => ['required', 'string', 'max:255'],
        ];

        if ($this->isAdmin()) {
            $rules['doctor_id'] = ['required', 'integer', 'exists:doctor_infos,id'];
        }

        $data = $request->validate($rules);

        $patientUser = User::where('role', 'patient')->find($data['patient_id']);
        if (!$patientUser) {
            return back()->withErrors(['patient_id' => 'Selected user is not a patient.'])->withInput();
        }

        // ✅ Doctor: امنع تغيير doctor_id
        if ($this->isDoctor()) {
            $data['doctor_id'] = $rx->doctor_id;
        }

        // تنظيف medicines
        $medicine = collect($data['medicine_name'])->map(fn($v) => trim((string) $v))->filter()->values()->all();
        $dosage = collect($data['dosage'])->map(fn($v) => trim((string) $v))->filter()->values()->all();
        $duration = collect($data['duration'])->map(fn($v) => trim((string) $v))->filter()->values()->all();

        $notes = collect($data['notes'] ?? [])
            ->map(fn($v) => is_null($v) ? null : trim((string) $v))
            ->values()
            ->all();

        $count = count($medicine);
        if ($count === 0 || count($dosage) !== $count || count($duration) !== $count) {
            return back()
                ->withErrors(['medicine_name' => 'Please fill medicine, dosage and duration for each row.'])
                ->withInput();
        }

        if (count($notes) < $count) {
            $notes = array_pad($notes, $count, null);
        }

        $data['medicine_name'] = $medicine;
        $data['dosage'] = $dosage;
        $data['duration'] = $duration;
        $data['notes'] = $notes;

        // ✅ NEW: rumor + analysis
        $data['rumor'] = collect($data['rumor_name'] ?? [])
            ->map(fn($v) => trim((string) $v))
            ->filter()
            ->values()
            ->all();

        $data['analysis'] = collect($data['analysis_name'] ?? [])
            ->map(fn($v) => trim((string) $v))
            ->filter()
            ->values()
            ->all();

        unset($data['rumor_name'], $data['analysis_name']);

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
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $deleted = $this->scopeByRole(Prescription::query())
            ->whereIn('id', $data['ids'])
            ->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', "Deleted {$deleted} prescription(s).");
    }
}
