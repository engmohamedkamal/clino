<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreDiagnosisRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;
class DiagnosisController extends Controller
{
    /* ================= Helpers ================= */
    private function isPatient(): bool
    {
        return (auth()->check() && (auth()->user()->role ?? '') === 'patient');
    }

    private function isAdminOrDoctor(): bool
    {
        return (auth()->check() && in_array(auth()->user()->role ?? '', ['admin', 'doctor']));
    }

    /* ================= Index (Search) ================= */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $query = Diagnosis::query()
            ->with(['patient:id,name', 'creator:id,name']);

        // ✅ patient يشوف تقاريره فقط
        if ($this->isPatient()) {
            $query->where('patient_id', auth()->id());
        }

        // ✅ Search
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('patient_name', 'like', "%{$q}%")
                   ->orWhere('public_diagnosis', 'like', "%{$q}%")
                   ->orWhere('private_diagnosis', 'like', "%{$q}%")
                   ->orWhereHas('creator', function ($c) use ($q) {
                       $c->where('name', 'like', "%{$q}%");
                   })
                   ->orWhereHas('patient', function ($p) use ($q) {
                       $p->where('name', 'like', "%{$q}%");
                   });
            });
        }

        $diagnoses = $query->latest()->paginate(5)->appends(['q' => $q]);

        return view('dashboard.diagnosis.index', compact('diagnoses', 'q'));
    }

    /* ================= Create ================= */
    public function create(Request $request)
    {
        // ✅ حماية إضافية (لو حد دخل بالرابط)
        if (!$this->isAdminOrDoctor()) {
            abort(403);
        }
$patients = User::where('role', 'patient')->latest()->get();
        $patient = $request->patient_name;
        $request->session()->put('patient_phone', $request->patient_phone);

        

        return view('dashboard.diagnosis.create', compact('patients','patient'));
    }

    /* ================= Store ================= */
  public function store(StoreDiagnosisRequest $request)
{
    if (!$this->isAdminOrDoctor()) {
        abort(403);
    }

    $data = $request->validated();
    $patientId = (string)($data['patient_id'] ?? '');

    $diagnosis = null; // ✅ هنا

    DB::transaction(function () use ($request, &$data, $patientId, &$diagnosis) {

        if ($patientId === '') {
            throw ValidationException::withMessages([
                'patient_id' => 'Please select a patient.',
            ]);
        }

        // ✅ Add new patient
        if ($patientId === '__new__') {

            $newName = trim((string) $request->input('patient_name_new'));
            if ($newName === '') {
                throw ValidationException::withMessages([
                    'patient_name_new' => 'New patient name is required.',
                ]);
            }

            $idNumber = trim((string) $request->input('id_number'));
            if ($idNumber === '') {
                $idNumber = now()->format('YmdHis') . random_int(100, 999);
            }

            $phone = now()->format('YmdHis') . random_int(100, 999);

            $patient = User::create([
                'name'      => $newName,
                'role'      => 'patient',
                'phone'     => $phone,
                'id_number' => $idNumber,
                'password'  => Hash::make('password'),
            ]);

            $data['patient_id']   = $patient->id;
            $data['patient_name'] = $patient->name;

        } else {

            if (!ctype_digit($patientId)) {
                throw ValidationException::withMessages([
                    'patient_id' => 'Selected patient not found.',
                ]);
            }

            $patient = User::query()
                ->where('role', 'patient')
                ->where('id', (int)$patientId)
                ->first();

            if (!$patient) {
                throw ValidationException::withMessages([
                    'patient_id' => 'Selected patient not found.',
                ]);
            }

            $data['patient_id']   = $patient->id;
            $data['patient_name'] = $patient->name;
        }

        $data['created_by'] = auth()->id();

        // ✅ امسكي التشخيص
        $diagnosis = Diagnosis::create([
            'patient_id'        => $data['patient_id'],
            'patient_name'      => $data['patient_name'],
            'public_diagnosis'  => $data['public_diagnosis'],
            'private_diagnosis' => $data['private_diagnosis'] ?? null,
            'created_by'        => $data['created_by'],
        ]);
    });

    // ✅ هنا الـ ID متاح
    return redirect()
        ->route('diagnoses.show', $diagnosis->id)
        ->with('success', 'Diagnosis added successfully.');
}


    /* ================= Show ================= */
    public function show(Diagnosis $diagnosis)
    {
        $diagnosis->load(['patient:id,name', 'creator:id,name']);

        // ✅ patient يشوف تقريره فقط
        if ($this->isPatient() && (int)$diagnosis->patient_id !== (int)auth()->id()) {
            abort(403);
        }

        return view('dashboard.diagnosis.show', compact('diagnosis'));
    }

    /* ================= Edit ================= */
    public function edit(Diagnosis $diagnosis)
    {
        if (!$this->isAdminOrDoctor()) {
            abort(403);
        }

        $patients = User::query()
            ->where('role', 'patient')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('dashboard.diagnosis.edit', compact('diagnosis', 'patients'));
    }

    /* ================= Update ================= */
    public function update(Request $request, Diagnosis $diagnosis)
    {
        if (!$this->isAdminOrDoctor()) {
            abort(403);
        }

        $data = $request->validate([
            'patient_id'        => ['nullable'],
            'patient_name_new'  => ['nullable', 'string', 'max:255'], // لو هتستخدم __new__ في edit
            'public_diagnosis'  => ['required', 'string', 'max:255'],
            'private_diagnosis' => ['nullable', 'string', 'max:255'],
        ]);

        // ✅ لو هتسمح بتغيير المريض من edit
        if (!empty($data['patient_id'])) {

            // لو اختار __new__ من edit (اختياري)
            if ((string)$data['patient_id'] === '__new__') {

                $newName = trim((string)($data['patient_name_new'] ?? ''));
                if ($newName === '') {
                    throw ValidationException::withMessages([
                        'patient_name_new' => 'New patient name is required.',
                    ]);
                }

                $idNumber = now()->format('YmdHis') . random_int(100, 999);

                $patient = User::create([
                    'name'      => $newName,
                    'role'      => 'patient',
                    'phone'     => $idNumber,
                    'id_number' => $idNumber,
                    'password'  => Hash::make('password'),
                ]);

                $data['patient_id']   = $patient->id;
                $data['patient_name'] = $patient->name;

            } else {

                $pid = (string)$data['patient_id'];
                if (!ctype_digit($pid)) {
                    throw ValidationException::withMessages([
                        'patient_id' => 'Selected patient not found.',
                    ]);
                }

                $patient = User::query()
                    ->where('role', 'patient')
                    ->where('id', (int)$pid)
                    ->first();

                if (!$patient) {
                    throw ValidationException::withMessages([
                        'patient_id' => 'Selected patient not found.',
                    ]);
                }

                $data['patient_id']   = $patient->id;
                $data['patient_name'] = $patient->name;
            }
        }

        // ✅ ما نحدّثش patient_name_new في diagnoses
        unset($data['patient_name_new']);

        $diagnosis->update($data);

        return redirect()
            ->route('diagnoses.show', $diagnosis)
            ->with('success', 'Diagnosis updated.');
    }

    /* ================= Destroy ================= */
    public function destroy(Diagnosis $diagnosis)
    {
        if (!$this->isAdminOrDoctor()) {
            abort(403);
        }

        $diagnosis->delete();

        return redirect()
            ->route('diagnoses.index')
            ->with('success', 'Diagnosis deleted.');
    }
      public function pdf(Diagnosis $diagnosis)
    {
        $patientPhone = session('patient_phone');
        $diagnosis->load(['patient', 'creator', 'creator.doctorInfo']);

        $setting = Setting::first();

        // صلاحيات عرض private diagnosis
        $role = auth()->user()->role ?? '';
        $canSeePrivate = in_array($role, ['admin', 'doctor']);

        // بيانات عرض
        $patient = $diagnosis->patient ?? null;

        $patientName = $patient->name ?? ($diagnosis->patient_name ?? '—');
        $patientId   = $patient->id ?? ($diagnosis->patient_id ?? '—');
        $doctorName  = $diagnosis->creator?->name ?? '—';

        $diagId = 'DX-' . str_pad($diagnosis->id, 6, '0', STR_PAD_LEFT);
        $issued = optional($diagnosis->created_at)->format('M d, Y H:i');

        // QR (اختياري) - social link
        $socialLink = $diagnosis->creator?->doctorInfo?->social_link;

        $pdf = Pdf::loadView('dashboard.diagnosis.pdf', compact(
            'diagnosis',
            'setting',
            'canSeePrivate',
            'patientName',
            'patientId',
            'doctorName',
            'diagId',
            'issued',
            'socialLink',
            'patientPhone'
        ))->setPaper('a4');

        return $pdf->stream("diagnosis-{$diagnosis->id}.pdf");
    }
}