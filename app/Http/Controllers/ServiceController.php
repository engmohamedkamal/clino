<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
     public function doctors(Service $service)
    {
        // assuming: Service has doctors() relationship
        $doctors = $service->doctors()
            ->with('user') // لو DoctorInfo مربوط بـ user
            ->wherePivot('active', true) // لو عندك active في pivot
            ->get();

        return view('services.doctors', compact('service', 'doctors'));
    }
    public function index(Request $request)
    {
        $query = Service::query();

        // Search by name
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $services = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.service.show', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('admin.service.add');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'nullable|boolean',
        ]);

        $data['status'] = $request->has('status');

        // Upload image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()
            ->route('service.index')
            ->with('success', 'Service added successfully.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        return view('admin.service.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'nullable|boolean',
        ]);

        $data['status'] = $request->has('status');

        // Replace image if uploaded
        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()
            ->route('service.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()
            ->route('service.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Bulk delete services.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:services,id',
        ]);

        $services = Service::whereIn('id', $request->ids)->get();

        foreach ($services as $service) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
        }

        Service::whereIn('id', $request->ids)->delete();

        return redirect()
            ->route('service.index')
            ->with('success', 'Selected services deleted successfully.');
    }
}
