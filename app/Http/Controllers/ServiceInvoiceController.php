<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\DoctorInfo;
use Illuminate\Http\Request;
use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreServiceInvoiceRequest;

class ServiceInvoiceController extends Controller
{
    /* ================= Index (Search) ================= */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $invoices = ServiceInvoice::query()
            ->with(['patient:id,name,phone', 'creator:id,name'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('invoice_no', 'like', "%{$q}%")
                        ->orWhere('patient_name', 'like', "%{$q}%")
                        ->orWhere('patient_phone', 'like', "%{$q}%")
                        ->orWhereHas('patient', function ($p) use ($q) {
                            $p->where('name', 'like', "%{$q}%")
                              ->orWhere('phone', 'like', "%{$q}%");
                        })
                        ->orWhereHas('creator', function ($c) use ($q) {
                            $c->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->appends(['q' => $q]);

        return view('admin.service_invoices.index', compact('invoices', 'q'));
    }

    /* ================= Create ================= */
    public function create(Request $request)
    {
        // ================= Patients =================
        $patients = User::query()
            ->where('role', 'patient')
            ->select('id', 'name', 'phone')
            ->orderBy('name')
            ->get();

        // ================= Doctor / Admin =================
        $user = auth()->user();
        $role = $user->role ?? '';

        $doctorInfo = null;
        $doctors = collect();          // للـ admin فقط
        $selectedDoctorId = null;

        if ($role === 'doctor') {

            $doctorInfo = DoctorInfo::query()
                ->where('user_id', $user->id)
                ->firstOrFail();

            $selectedDoctorId = $doctorInfo->id;

        } else {
            // admin_or_doctor middleware عندك، فهنا غالباً admin

            $doctors = DoctorInfo::query()
                ->with(['user:id,name']) // لازم يكون عندك relation user في DoctorInfo
                ->select('id', 'user_id')
                ->orderBy('id')
                ->get();

            $selectedDoctorId = $request->get('doctor_id');

            if ($selectedDoctorId && ctype_digit((string) $selectedDoctorId)) {
                $doctorInfo = DoctorInfo::query()->find((int) $selectedDoctorId);
            }

            if (!$doctorInfo) {
                $doctorInfo = DoctorInfo::query()->firstOrFail();
                $selectedDoctorId = $doctorInfo->id;
            }
        }

        // ================= Services (ALL + effective price) =================
        // ✅ لازم نجيب price عشان fallback للخدمات اللي ملهاش pivot
        $services = Service::query()
            ->select('id', 'name'   )
            ->orderBy('name')
            ->get();

        // pivot prices للدكتور: [service_id => pivot_price]
        $pivotPrices = $doctorInfo
            ? $doctorInfo->services()
                ->wherePivot('active', 1)
                ->pluck('doctor_service.price', 'services.id')
                ->toArray()
            : [];

        // نزود لكل خدمة:
        // - effective_price = pivot price لو موجود وإلا base price
        // - has_pivot = هل لها pivot عند الدكتور؟
        $services = $services->map(function ($s) use ($pivotPrices) {
            $sid = (int) $s->id;

            $hasPivot = array_key_exists($sid, $pivotPrices);
            $effective = (float) ($hasPivot ? $pivotPrices[$sid] : ($s->price ?? 0));

            $s->setAttribute('has_pivot', $hasPivot);
            $s->setAttribute('effective_price', $effective);

            return $s;
        });

        return view('admin.service_invoices.create', compact(
            'patients',
            'services',
            'doctorInfo',
            'doctors',
            'selectedDoctorId'
        ));
    }

    /* ================= Store ================= */
 public function store(StoreServiceInvoiceRequest $request)
{
    $data = $request->validated();

    DB::transaction(function () use ($request, &$data) {

        // ================= Doctor pick (doctor/admin) =================
        $user = auth()->user();
        $role = $user->role ?? '';
        $doctorInfo = null;

        if ($role === 'doctor') {

            $doctorInfo = DoctorInfo::query()
                ->where('user_id', $user->id)
                ->firstOrFail();

        } else {

            $doctorId = (string) $request->input('doctor_id', '');

            if ($doctorId === '' || !ctype_digit($doctorId)) {
                throw ValidationException::withMessages([
                    'doctor_id' => 'Please select a doctor.',
                ]);
            }

            $doctorInfo = DoctorInfo::query()->find((int) $doctorId);

            if (!$doctorInfo) {
                throw ValidationException::withMessages([
                    'doctor_id' => 'Selected doctor not found.',
                ]);
            }
        }

        // ================= Patient =================
        $patientId = (string) ($data['patient_id'] ?? '');

        if ($patientId === '__new__') {

            $newName = trim((string) $request->input('patient_name_new'));
            if ($newName === '') {
                throw ValidationException::withMessages([
                    'patient_name_new' => 'New patient name is required.',
                ]);
            }

            $phone = trim((string) $request->input('patient_phone_new'));
            if ($phone === '') $phone = '01111111111';

            $idNumber = trim((string) $request->input('id_number'));
            if ($idNumber === '') {
                $idNumber = now()->format('YmdHis') . random_int(100, 999);
            }

            $patient = User::create([
                'name'      => $newName,
                'role'      => 'patient',
                'phone'     => $phone,
                'id_number' => $idNumber,
                'password'  => Hash::make('password'),
            ]);

            $data['patient_id']    = $patient->id;
            $data['patient_name']  = $patient->name;
            $data['patient_phone'] = $patient->phone;
            $data['patient_code']  = $request->input('patient_code_new');

        } else {

            if (!ctype_digit((string) $patientId)) {
                throw ValidationException::withMessages([
                    'patient_id' => 'Selected patient is invalid.',
                ]);
            }

            $patient = User::query()
                ->where('role', 'patient')
                ->where('id', (int) $patientId)
                ->first();

            if (!$patient) {
                throw ValidationException::withMessages([
                    'patient_id' => 'Selected patient not found.',
                ]);
            }

            $data['patient_id']    = $patient->id;
            $data['patient_name']  = $patient->name;
            $data['patient_phone'] = $patient->phone;
        }

        // ================= Items =================
        $items = $data['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            throw ValidationException::withMessages([
                'items' => 'Please add at least 1 service item.',
            ]);
        }

        // ✅ pivot prices للدكتور: [service_id => price]
        $pivotPrices = $doctorInfo->services()
            ->wherePivot('active', 1)
            ->pluck('doctor_service.price', 'services.id')
            ->toArray();

        // ================= Totals =================
        $subtotal = 0;

        foreach ($items as $idx => &$it) {

            $serviceId = $it['service_id'] ?? null;

            if (!$serviceId || !ctype_digit((string)$serviceId)) {
                throw ValidationException::withMessages([
                    "items.$idx.service_id" => 'Service is required.',
                ]);
            }

            $serviceId = (int) $serviceId;

            // ✅ name fallback لو الواجهة مبعتتش الاسم
            if (empty($it['service_name'])) {
                $serviceName = Service::query()->whereKey($serviceId)->value('name');
                $it['service_name'] = $serviceName ?: '—';
            }

            $qty = max(1, (int)($it['qty'] ?? 1));

            if (array_key_exists($serviceId, $pivotPrices)) {
                // ✅ لو Pivot موجود => سعر تلقائي من doctor_service
                $unit = max(0, (float) $pivotPrices[$serviceId]);
            } else {
                // ✅ لو مفيش pivot => admin لازم يدخل السعر بنفسه
                $unit = (float)($it['unit_price'] ?? 0);

                if ($unit <= 0) {
                    throw ValidationException::withMessages([
                        "items.$idx.unit_price" => 'Price is required for this service.',
                    ]);
                }
            }

            // ✅ نخلي الداتا مظبوطة حتى لو الواجهة بعتت unit_price غلط
            $it['unit_price'] = $unit;
            $it['qty'] = $qty;

            $subtotal += $qty * $unit;
        }
        unset($it);

        $discount   = max(0, (float)($data['discount'] ?? 0));
        $taxPercent = max(0, min(100, (float)($data['tax_percent'] ?? 0)));

        $taxBase   = max(0, $subtotal - $discount);
        $taxAmount = round($taxBase * ($taxPercent / 100), 2);
        $total     = round($taxBase + $taxAmount, 2);

        // ================= Create Invoice =================
        $invoice = ServiceInvoice::create([
            'invoice_no'         => 'TMP-' . uniqid(),

            'patient_id'         => $data['patient_id'] ?? null,
            'patient_name'       => $data['patient_name'] ?? null,
            'patient_phone'      => $data['patient_phone'] ?? null,
            'patient_code'       => $data['patient_code'] ?? null,

            'insurance_provider' => $data['insurance_provider'] ?? null,
            'notes'              => $data['notes'] ?? null,

            // ✅ doctor linkage (لازم الأعمدة تكون موجودة)
            'doctor_info_id'     => $doctorInfo->id ?? null,
            'doctor_id'          => $doctorInfo->user_id ?? null,

            'payment_method'     => $data['payment_method'],
            'payment_status'     => $data['payment_status'],

            'subtotal'           => $subtotal,
            'discount'           => $discount,
            'tax_percent'        => $taxPercent,
            'tax_amount'         => $taxAmount,
            'total'              => $total,

            'created_by'         => auth()->id(),
            'issued_at'          => now(),
        ]);

        $invoiceNo = 'INV-' . now()->format('Y') . '-' . str_pad((string)$invoice->id, 6, '0', STR_PAD_LEFT);
        $invoice->update(['invoice_no' => $invoiceNo]);

        // ================= Items Create =================
        foreach ($items as $it) {

            $qty  = max(1, (int)($it['qty'] ?? 1));
            $unit = max(0, (float)($it['unit_price'] ?? 0));
            $lineSubtotal = round($qty * $unit, 2);

            ServiceInvoiceItem::create([
                'service_invoice_id' => $invoice->id,
                'service_id'         => $it['service_id'],
                'service_name'       => $it['service_name'] ?? null,

                // optional from UI
                'doctor_name'        => $it['doctor_name'] ?? null,
                'department'         => $it['department'] ?? null,

                'qty'                => $qty,
                'unit_price'         => $unit,
                'subtotal'           => $lineSubtotal,
            ]);
        }
    });

    return redirect()
        ->route('service-invoices.index')
        ->with('success', 'Invoice created successfully.');
}


    /* ================= Show ================= */
    public function show(ServiceInvoice $service_invoice)
    {
        $service_invoice->load([
            'patient:id,name,phone',
            'creator:id,name',
            'items',
        ]);

        return view('admin.service_invoices.show', [
            'invoice' => $service_invoice
        ]);
    }

    /* ================= Edit ================= */
    public function edit(Request $request, ServiceInvoice $service_invoice)
    {
        $service_invoice->load(['items']);

        $user = auth()->user();
        $role = $user->role ?? '';

        // Patients
        $patients = User::query()
            ->where('role', 'patient')
            ->select('id', 'name', 'phone')
            ->orderBy('name')
            ->get();

        // Doctor selection
        $doctorInfo = null;
        $doctors = collect();
        $selectedDoctorId = null;

        if ($role === 'doctor') {

            $doctorInfo = DoctorInfo::query()
                ->where('user_id', $user->id)
                ->firstOrFail();

            $selectedDoctorId = $doctorInfo->id;

        } else {
            // admin
            $doctors = DoctorInfo::query()
                ->with(['user:id,name'])
                ->select('id', 'user_id')
                ->orderBy('id')
                ->get();

            // priority: request doctor_id -> invoice doctor_info_id -> first
            $selectedDoctorId = $request->get('doctor_id')
                ?: ($service_invoice->doctor_info_id ?? null);

            if ($selectedDoctorId && ctype_digit((string) $selectedDoctorId)) {
                $doctorInfo = DoctorInfo::query()->find((int) $selectedDoctorId);
            }

            if (!$doctorInfo) {
                $doctorInfo = DoctorInfo::query()->firstOrFail();
                $selectedDoctorId = $doctorInfo->id;
            }
        }

        // Services: ALL + effective price based on selected doctor
        $services = Service::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $pivotPrices = $doctorInfo
            ? $doctorInfo->services()
                ->wherePivot('active', 1)
                ->pluck('doctor_service.price', 'services.id')
                ->toArray()
            : [];

        $services = $services->map(function ($s) use ($pivotPrices) {
            $sid = (int) $s->id;

            $hasPivot = array_key_exists($sid, $pivotPrices);
            $effective = (float) ($hasPivot ? $pivotPrices[$sid] : ($s->price ?? 0));

            $s->setAttribute('has_pivot', $hasPivot);
            $s->setAttribute('effective_price', $effective);

            return $s;
        });

        return view('admin.service_invoices.edit', [
            'invoice'          => $service_invoice,
            'patients'         => $patients,
            'services'         => $services,
            'doctors'          => $doctors,
            'selectedDoctorId' => $selectedDoctorId,
            'doctorInfo'       => $doctorInfo,
        ]);
    }

    /* ================= Update ================= */
 public function update(Request $request, ServiceInvoice $service_invoice)
{
    $data = $request->validate([
        'doctor_id' => ['nullable'], // admin فقط (اختياري حسب الواجهة)

        'patient_id' => ['required'],
        'patient_name_new' => ['nullable', 'string', 'max:255'],
        'patient_phone_new' => ['nullable', 'string', 'max:30'],
        'patient_code_new' => ['nullable', 'string', 'max:50'],

        'insurance_provider' => ['nullable', 'string', 'max:255'],
        'notes' => ['nullable', 'string', 'max:5000'],

        'payment_method' => ['required', 'in:card,cash,insurance,wallet'],
        'payment_status' => ['required', 'in:pending,paid,partial'],

        'discount' => ['nullable', 'numeric', 'min:0'],
        'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

        'items' => ['required', 'array', 'min:1'],
        'items.*.service_id' => ['required', 'integer'],
        'items.*.service_name' => ['nullable', 'string', 'max:255'],
        'items.*.doctor_name' => ['nullable', 'string', 'max:255'],
        'items.*.department' => ['nullable', 'string', 'max:255'],
        'items.*.qty' => ['required', 'integer', 'min:1'],

        // ✅ admin ممكن يكتبها، والدكتور هنoverride من pivot
        'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
    ]);

    DB::transaction(function () use ($request, &$data, $service_invoice) {

        // ================= Doctor pick (doctor/admin) =================
        $user = auth()->user();
        $role = $user->role ?? '';
        $doctorInfo = null;

        if ($role === 'doctor') {

            $doctorInfo = DoctorInfo::query()
                ->where('user_id', $user->id)
                ->firstOrFail();

        } else {
            // admin: لو مبعتش doctor_id ناخد الموجود في الفاتورة
            $doctorId = (string) $request->input('doctor_id', (string)($service_invoice->doctor_info_id ?? ''));

            if ($doctorId !== '' && ctype_digit($doctorId)) {
                $doctorInfo = DoctorInfo::query()->find((int) $doctorId);
            }

            // fallback: لو مفيش/غلط → خدي أول دكتور (أو ارمي error لو تحبي)
            if (!$doctorInfo) {
                $doctorInfo = DoctorInfo::query()->firstOrFail();
            }
        }

        // ================= Patient =================
        $pid = (string) ($data['patient_id'] ?? '');

        if ($pid === '__new__') {

            $newName = trim((string) $request->input('patient_name_new'));
            if ($newName === '') {
                throw ValidationException::withMessages([
                    'patient_name_new' => 'New patient name is required.',
                ]);
            }

            $phone = trim((string) $request->input('patient_phone_new'));
            if ($phone === '') $phone = '01111111111';

            $idNumber = trim((string) $request->input('id_number'));
            if ($idNumber === '') {
                $idNumber = now()->format('YmdHis') . random_int(100, 999);
            }

            $patient = User::create([
                'name' => $newName,
                'role' => 'patient',
                'phone' => $phone,
                'id_number' => $idNumber,
                'password' => Hash::make('password'),
            ]);

            $data['patient_id']    = $patient->id;
            $data['patient_name']  = $patient->name;
            $data['patient_phone'] = $patient->phone;
            $data['patient_code']  = $request->input('patient_code_new');

        } else {

            if (!ctype_digit((string) $pid)) {
                throw ValidationException::withMessages([
                    'patient_id' => 'Selected patient is invalid.',
                ]);
            }

            $patient = User::query()
                ->where('role', 'patient')
                ->where('id', (int) $pid)
                ->first();

            if (!$patient) {
                throw ValidationException::withMessages([
                    'patient_id' => 'Selected patient not found.',
                ]);
            }

            $data['patient_id']    = $patient->id;
            $data['patient_name']  = $patient->name;
            $data['patient_phone'] = $patient->phone;
        }

        // ================= Items =================
        $items = $data['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            throw ValidationException::withMessages([
                'items' => 'Please add at least 1 service item.',
            ]);
        }

        // ✅ pivot prices للدكتور: [service_id => price]
        $pivotPrices = $doctorInfo->services()
            ->wherePivot('active', 1)
            ->pluck('doctor_service.price', 'services.id')
            ->toArray();

        // ================= Totals (pivot if exists else admin unit_price) =================
        $subtotal = 0;

        foreach ($items as $idx => &$it) {

            $serviceId = $it['service_id'] ?? null;
            if (!$serviceId || !ctype_digit((string)$serviceId)) {
                throw ValidationException::withMessages([
                    "items.$idx.service_id" => 'Service is required.',
                ]);
            }

            $serviceId = (int) $serviceId;

            // ✅ تأكيد إن الخدمة موجودة
            $serviceName = Service::query()->whereKey($serviceId)->value('name');
            if (!$serviceName) {
                throw ValidationException::withMessages([
                    "items.$idx.service_id" => 'Service not found.',
                ]);
            }

            // ✅ الاسم (لو الواجهة مبعتتش)
            if (empty($it['service_name'])) {
                $it['service_name'] = $serviceName;
            }

            $qty = max(1, (int)($it['qty'] ?? 1));

            if (array_key_exists($serviceId, $pivotPrices)) {
                // ✅ Pivot موجود => سعر تلقائي
                $unit = max(0, (float)$pivotPrices[$serviceId]);
            } else {
                // ✅ مفيش pivot => admin لازم يكون مدخل unit_price
                $unit = (float)($it['unit_price'] ?? 0);

                if ($unit <= 0) {
                    throw ValidationException::withMessages([
                        "items.$idx.unit_price" => 'Price is required for this service.',
                    ]);
                }
            }

            // ✅ override للوحدة والكمية
            $it['qty'] = $qty;
            $it['unit_price'] = $unit;

            $subtotal += $qty * $unit;
        }
        unset($it);

        $discount   = max(0, (float)($data['discount'] ?? 0));
        $taxPercent = max(0, min(100, (float)($data['tax_percent'] ?? 0)));

        $taxBase   = max(0, $subtotal - $discount);
        $taxAmount = round($taxBase * ($taxPercent / 100), 2);
        $total     = round($taxBase + $taxAmount, 2);

        // ================= Update invoice header =================
        $service_invoice->update([
            'patient_id'         => $data['patient_id'] ?? null,
            'patient_name'       => $data['patient_name'] ?? null,
            'patient_phone'      => $data['patient_phone'] ?? null,
            'patient_code'       => $data['patient_code'] ?? null,

            'insurance_provider' => $data['insurance_provider'] ?? null,
            'notes'              => $data['notes'] ?? null,

            'doctor_info_id'     => $doctorInfo->id ?? null,
            'doctor_id'          => $doctorInfo->user_id ?? null,

            'payment_method'     => $data['payment_method'],
            'payment_status'     => $data['payment_status'],

            'subtotal'           => $subtotal,
            'discount'           => $discount,
            'tax_percent'        => $taxPercent,
            'tax_amount'         => $taxAmount,
            'total'              => $total,
        ]);

        // ================= Replace items =================
        $service_invoice->items()->delete();

        foreach ($items as $it) {

            $qty  = max(1, (int)($it['qty'] ?? 1));
            $unit = max(0, (float)($it['unit_price'] ?? 0));
            $lineSubtotal = round($qty * $unit, 2);

            ServiceInvoiceItem::create([
                'service_invoice_id' => $service_invoice->id,
                'service_id'         => $it['service_id'],
                'service_name'       => $it['service_name'] ?? null,
                'doctor_name'        => $it['doctor_name'] ?? null,
                'department'         => $it['department'] ?? null,
                'qty'                => $qty,
                'unit_price'         => $unit,
                'subtotal'           => $lineSubtotal,
            ]);
        }
    });

    return redirect()
        ->route('service-invoices.index', $service_invoice)
        ->with('success', 'Invoice updated successfully.');
}

    /* ================= Destroy ================= */
    public function destroy(ServiceInvoice $service_invoice)
    {
        $service_invoice->delete();

        return redirect()
            ->route('service-invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
