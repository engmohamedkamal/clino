<?php
namespace App\Http\Controllers;

use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserInfoController extends Controller
{
    public function show()
    {
        $info = Auth::user()->info; // hasOne
        return view('my-info.show', compact('info'));
    }

    public function create()
    {
        // لو المستخدم عنده بيانات بالفعل، ممنوع create
        if (Auth::user()->info) {
            return redirect()->route('my-info.edit')
                ->with('info', 'You already added your info. You can update it.');
        }

        return view('my-info.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->info) {
            return redirect()->route('my-info.edit')
                ->with('info', 'You already added your info. You can update it.');
        }

        $data = $this->validated($request);

        // image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('user-info', 'public');
        }

        $data['user_id'] = Auth::id();

        UserInfo::create($data);

        return redirect()->route('my-info.show')->with('success', 'Info saved successfully.');
    }

    public function edit()
    {
        $info = Auth::user()->info;

        // لو مفيش بيانات، يروح create
        if (!$info) {
            return redirect()->route('my-info.create')
                ->with('info', 'Please add your info first.');
        }

        return view('my-info.edit', compact('info'));
    }

    public function update(Request $request)
    {
        $info = Auth::user()->info;

        if (!$info) {
            return redirect()->route('my-info.create')
                ->with('info', 'Please add your info first.');
        }

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            // delete old if exists
            if ($info->image) {
                Storage::disk('public')->delete($info->image);
            }
            $data['image'] = $request->file('image')->store('user-info', 'public');
        }

        $info->update($data);

        return redirect()->route('my-info.show')->with('success', 'Info updated successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'availability_schedule' => ['nullable','string','max:255'],
            'gender' => ['nullable','string','max:50'],
            'dob' => ['nullable','date'],
            'specialization' => ['nullable','string','max:255'],
            'license_number' => ['nullable','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'image' => ['nullable','image','max:2048'], // 2MB
            'about' => ['nullable','string'],
        ]);
    }
}
