<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\DoctorInfo;
use App\Models\Service;
use Illuminate\Http\Request;

class DoctorServicesBulkController extends Controller
{
    
   public function bulkEdit()
    {
        $user = auth()->user();

        $doctorsQuery = DoctorInfo::with(['user', 'services'])->orderBy('id');

        if ($user->role !== 'admin') {
            $doctorsQuery->where('user_id', $user->id);
        }

        $doctors  = $doctorsQuery->get();
        $services = Service::orderBy('name')->get();

        return view('admin.doctors.services-bulk', compact('doctors', 'services'));
    }

    public function bulkUpdate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'doctors' => ['required', 'array'],

            'doctors.*.services' => ['nullable', 'array'],
            'doctors.*.services.*' => ['integer', 'exists:services,id'],

            'doctors.*.pivot' => ['nullable', 'array'],
            'doctors.*.pivot.*.price' => ['required', 'numeric', 'min:0'],
            'doctors.*.pivot.*.duration' => ['required', 'integer', 'min:1'],
            'doctors.*.pivot.*.active' => ['required', 'boolean'],
        ]);

        $allowedDoctorIds = DoctorInfo::query()
            ->when(!$user->hasRole('admin'), fn ($q) => $q->where('user_id', $user->id))
            ->pluck('id')
            ->toArray();

        foreach ($validated['doctors'] as $doctorInfoId => $data) {

            if (!in_array((int) $doctorInfoId, $allowedDoctorIds, true)) {
                continue;
            }

            $doctor = DoctorInfo::find($doctorInfoId);
            if (!$doctor) continue;

            if (Gate::denies('manage-doctor-services', $doctor)) {
                continue;
            }

            $serviceIds = $data['services'] ?? [];
            $pivotInput = $data['pivot'] ?? [];

            $syncData = [];

            foreach ($serviceIds as $sid) {
                $p = $pivotInput[$sid] ?? [];

                $syncData[$sid] = [
                    'price'    => $p['price'] ?? 0,
                    'duration' => $p['duration'] ?? 1,
                    'active'   => isset($p['active']) ? (bool) $p['active'] : true,
                ];
            }

            $doctor->services()->sync($syncData);
        }

        return back()->with('success', 'Services updated successfully.');
    }
}
