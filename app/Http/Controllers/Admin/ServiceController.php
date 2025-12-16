<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.service.add');
    }
    public function store(ServiceRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/services'), $filename);
            $data['image'] = 'uploads/services/' . $filename;
        }

        Service::create($data);

        return redirect()->route('service.show')
            ->with('success', 'Service created successfully');
    }
    public function show()
    {
        $services = Service::latest()->paginate(3);
        return view('admin.service.show', compact('services'));
    }
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.service.edit', compact('service'));
    }
    public function update(ServiceRequest $request, $id)
    {
        $service = Service::findOrFail($id);
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/services'), $filename);
            $data['image'] = 'uploads/services/' . $filename;
            if ($service->image && file_exists(public_path($service->image))) {
                unlink(public_path($service->image));
            }
        } else {
            $data['image'] = $service->image;
        }

        $service->update($data);

        return redirect()->route('service.show')
            ->with('success', 'Service updated successfully');
    }
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect()->route('service.show')
            ->with('success', 'Service deleted successfully');
    }
}
