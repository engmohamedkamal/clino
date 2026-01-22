<?php

namespace App\Http\Controllers;

use App\Models\DoctorInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\DoctorInfoRequest;

class DoctorInfoController extends Controller
{
    public function show($id)
    {
        $info = DoctorInfo::with('user')->findOrFail($id);
        $user = $info->user;

        return view('dashboard.doctor.show', compact('info', 'user'));
    }

    public function create()
    {
        $info = auth()->user()->doctorInfo;

        if ($info) {
            return redirect()
                ->route('doctor-info.edit', $info->id)
                ->with('info', 'You already have info. You can update it.');
        }

        return view('dashboard.doctor.index', ['info' => null]);
    }

    public function edit(DoctorInfo $doctorInfo)
    {
        $this->authorizeOwner($doctorInfo);

        return view('dashboard.doctor.index', ['info' => $doctorInfo]);
    }

    public function store(DoctorInfoRequest $request)
    {
        $user = auth()->user();

        if ($user->doctorInfo) {
            return redirect()
                ->route('doctor-info.show', $user->doctorInfo->id)
                ->with('info', 'You already have info. You can update it.');
        }

        $data = $this->cleanMultiValues($request->validated());
        $data['user_id'] = $user->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        $info = DoctorInfo::create($data);

        return redirect()
            ->route('doctor-info.show', $info->id)
            ->with('success', 'Doctor info saved successfully.');
    }

    public function update(DoctorInfoRequest $request, DoctorInfo $doctorInfo)
    {
        $this->authorizeOwner($doctorInfo);

        $data = $this->cleanMultiValues($request->validated());

        if ($request->hasFile('image')) {
            if ($doctorInfo->image && Storage::disk('public')->exists($doctorInfo->image)) {
                Storage::disk('public')->delete($doctorInfo->image);
            }

            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        $doctorInfo->update($data);

        return redirect()
            ->route('doctor-info.show', $doctorInfo->id)
            ->with('success', 'Doctor info updated successfully.');
    }

    public function list(Request $request)
    {
        $query = DoctorInfo::with('user');

        if ($request->filled('q')) {
            $search = $request->q;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $doctors = $query->paginate(9)->withQueryString();

        return view('dashboard.doctor.list', compact('doctors'));
    }

    /**
     * ✅ تنظيف وتحضير البيانات multi-valued
     */
    private function cleanMultiValues(array $data): array
    {
        // ✅ availability_schedule: array of rows [{day, from, to}, ...]
        $data['availability_schedule'] = $this->cleanAvailabilityRows($data['availability_schedule'] ?? null);

        // ✅ Specialization: ["General Surgery", "Cardiology"]
        $data['Specialization'] = $this->cleanStringArray($data['Specialization'] ?? null);

        // ✅ activities: ["Clinic rounds", "Research"]
        $data['activities'] = $this->cleanStringArray($data['activities'] ?? null);

        // ✅ NEW: visit_types: [{type:"كشف", price:300}, ...]
        $data['visit_types'] = $this->cleanVisitTypes($data['visit_types'] ?? null);

        // ✅ skills: [{name:"Operations", value:45}, ...]
        $skills = $data['skills'] ?? null;
        if (is_array($skills)) {
            $clean = [];
            foreach ($skills as $s) {
                $name  = isset($s['name']) ? trim((string) $s['name']) : '';
                $value = isset($s['value']) ? (int) $s['value'] : null;

                if ($name === '' || $value === null) continue;

                $value = max(0, min(100, $value));
                $clean[] = ['name' => $name, 'value' => $value];
            }
            $data['skills'] = $clean;
        } else {
            $data['skills'] = [];
        }

        return $data;
    }

 
    private function cleanVisitTypes($rows): array
    {
        if (!is_array($rows)) return [];

        $clean = [];

        foreach ($rows as $r) {
            if (!is_array($r)) continue;

            $type  = isset($r['type']) ? trim((string) $r['type']) : '';
            $price = isset($r['price']) ? $r['price'] : null;

            // تجاهل الصف الفاضي
            if ($type === '' && ($price === null || $price === '')) continue;

            // لازم الاتنين موجودين
            if ($type === '') continue;

            // price رقم
            if ($price === null || $price === '') continue;
            $price = (float) $price;

            // يمنع قيم سالبة
            if ($price < 0) continue;

            $clean[] = [
                'type'  => $type,
                'price' => $price,
            ];
        }

        return array_values($clean);
    }

    /**
     * ✅ تنظيف availability rows
     */
    private function cleanAvailabilityRows($rows): array
    {
        if (!is_array($rows)) return [];

        $allowedDays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $clean = [];

        foreach ($rows as $r) {
            if (!is_array($r)) continue;

            $day  = isset($r['day'])  ? trim((string) $r['day'])  : '';
            $from = isset($r['from']) ? trim((string) $r['from']) : '';
            $to   = isset($r['to'])   ? trim((string) $r['to'])   : '';

            if ($day === '' && $from === '' && $to === '') continue;
            if ($day === '' || $from === '' || $to === '') continue;

            if (!in_array($day, $allowedDays, true)) continue;

            if (!preg_match('/^\d{2}:\d{2}$/', $from)) continue;
            if (!preg_match('/^\d{2}:\d{2}$/', $to)) continue;

            if ($from >= $to) continue;

            $clean[] = [
                'day'  => $day,
                'from' => $from,
                'to'   => $to,
            ];
        }

        return array_values($clean);
    }

    private function cleanStringArray($arr): array
    {
        if (!is_array($arr)) return [];

        $arr = array_map(fn ($v) => trim((string) $v), $arr);
        $arr = array_filter($arr, fn ($v) => $v !== '');
        return array_values($arr);
    }

    private function authorizeOwner(DoctorInfo $doctorInfo): void
    {
        if ($doctorInfo->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
