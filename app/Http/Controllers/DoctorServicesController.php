<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\DoctorInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DoctorServicesController extends Controller
{
    public function edit(Request $request, $doctorInfoId = null)
    {
        $doctorInfo = $doctorInfoId
            ? DoctorInfo::with('services')->findOrFail($doctorInfoId)
            : Auth::user()->doctorInfo()->with('services')->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $doctorInfo->user_id !== Auth::id()) {
            abort(403);
        }

        $services = Service::orderBy('name')->get();
        $selected = $doctorInfo->services->keyBy('id'); 
        return view('admin.doctors.services-edit', compact('doctorInfo', 'services', 'selected'));
    }

    public function update(Request $request, $doctorInfoId = null)
    {
        $doctorInfo = $doctorInfoId
            ? DoctorInfo::findOrFail($doctorInfoId)
            : Auth::user()->doctorInfo()->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $doctorInfo->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'services' => ['nullable', 'array'],
            'services.*' => ['integer', 'exists:services,id'],
            'pivot' => ['nullable', 'array'],
            'pivot.*.price' => ['required', 'numeric', 'min:0'],
            'pivot.*.duration' => ['required', 'integer', 'min:1'],
            'pivot.*.active' => ['required', 'boolean'],
        ]);

        $serviceIds = $validated['services'] ?? [];
        $pivotInput = $validated['pivot'] ?? [];
        $syncData = [];

        foreach ($serviceIds as $sid) {
            $p = $pivotInput[$sid] ?? [];

            $syncData[$sid] = [
                'price'    => $p['price'] ?? null,
                'duration' => $p['duration'] ?? null,
                'active'   => isset($p['active']) ? (bool)$p['active'] : true,
            ];
        }
        $doctorInfo->services()->sync($syncData);

        return redirect()
            ->back()
            ->with('success', 'Services updated successfully.');
    }
    public function toggle(Request $request, $doctorInfoId, $serviceId)
    {
        $doctorInfo = DoctorInfo::findOrFail($doctorInfoId);

        if (!Auth::user()->hasRole('admin') && $doctorInfo->user_id !== Auth::id()) {
            abort(403);
        }

        $exists = $doctorInfo->services()->where('services.id', $serviceId)->exists();
        if (!$exists) abort(404);

        $current = $doctorInfo->services()->where('services.id', $serviceId)->first();
        $newActive = !$current->pivot->active;

        $doctorInfo->services()->updateExistingPivot($serviceId, [
            'active' => $newActive
        ]);

        return back()->with('success', 'Service status updated.');
    }   

}
