<?php
namespace App\Http\Controllers;

use App\Http\Requests\DoctorInfoRequest;
use App\Models\DoctorInfo;
use Illuminate\Support\Facades\Storage;

class DoctorInfoController extends Controller
{
    public function show()
    {
        $info = auth()->user()->doctorInfo;
        return view('dashboard.doctor.show', compact('info'));
    }

    public function create()
    {
        $info = auth()->user()->doctorInfo;
        if ($info) {
            return redirect()->route('doctor-info.edit', $info->id)
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

        // Create مرة واحدة فقط
        if ($user->doctorInfo) {
            return redirect()->route('doctor-info.show')
                ->with('info', 'You already have info. You can update it.');
        }

        $data = $this->cleanMultiValues($request->validated());
        $data['user_id'] = $user->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        DoctorInfo::create($data);

        return redirect()->route('doctor-info.show')
            ->with('success', 'Doctor info saved successfully.');
    }

    public function update(DoctorInfoRequest $request, DoctorInfo $doctorInfo)
    {
        // owner only
        if ($doctorInfo->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $this->cleanMultiValues($request->validated());

        if ($request->hasFile('image')) {
            // delete old
            if ($doctorInfo->image && Storage::disk('public')->exists($doctorInfo->image)) {
                Storage::disk('public')->delete($doctorInfo->image);
            }

            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        $doctorInfo->update($data);

        return redirect()->route('doctor-info.show')
            ->with('success', 'Doctor info updated successfully.');
    }

    /**
     * ✅ تنظيف وتحضير المصفوفات multi-valued
     */
    private function cleanMultiValues(array $data): array
    {
        // availability_schedule: ["Mon 9-2", "Tue 9-2"]
        $data['availability_schedule'] = $this->cleanStringArray($data['availability_schedule'] ?? null);

        // Specialization: ["General Surgery", "Cardiology"]
        $data['Specialization'] = $this->cleanStringArray($data['Specialization'] ?? null);

        // activities: ["Clinic rounds", "Research"]
        $data['activities'] = $this->cleanStringArray($data['activities'] ?? null);

        // skills: [{name:"Operations", value:45}, ...]
        $skills = $data['skills'] ?? null;
        if (is_array($skills)) {
            $clean = [];
            foreach ($skills as $s) {
                $name = isset($s['name']) ? trim((string)$s['name']) : '';
                $value = isset($s['value']) ? (int)$s['value'] : null;

                if ($name === '' || $value === null) {
                    continue;
                }

                $value = max(0, min(100, $value));
                $clean[] = ['name' => $name, 'value' => $value];
            }
            $data['skills'] = $clean;
        } else {
            $data['skills'] = [];
        }

        return $data;
    }

    private function cleanStringArray($arr): array
    {
        if (!is_array($arr)) return [];
        $arr = array_map(fn($v) => trim((string)$v), $arr);
        $arr = array_filter($arr, fn($v) => $v !== '');
        return array_values($arr);
    }

    private function authorizeOwner(DoctorInfo $doctorInfo): void
    {
        if ($doctorInfo->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
