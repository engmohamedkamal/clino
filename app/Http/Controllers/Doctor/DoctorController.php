<?php

namespace App\Http\Controllers\Doctor;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRequest;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
public function index()
{
    $id = Auth::id();
    abort_unless(Auth::id() == $id, 403);
    $doctor = Doctor::where('user_id', $id)->first() ?? new Doctor();
    return view('doctor.info.add', compact('doctor'));
}

public function store(DoctorRequest $request)
{
    $id = Auth::id();
    $doctor = Doctor::where('user_id', $id)->first();

    $data = $request->validated();
    $data['user_id'] = $id;

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('uploads/doctors'), $filename);
        $data['image'] = 'uploads/doctors/' . $filename;
    } elseif ($doctor) {
        $data['image'] = $doctor->image; // يفضل الصورة القديمة
    }

    Doctor::updateOrCreate(['user_id' => $id], $data);

    return redirect()->route('doctor')->with('success', 'Doctor info saved successfully');
}

}
