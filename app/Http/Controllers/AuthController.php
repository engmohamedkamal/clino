<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register()
    {
        return view('register');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string', 'regex:/^[0-9]+$/','digits:11'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('phone'))
                ->withErrors(['phone' => __('auth.failed')]);
        }

        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Logging successful');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|min:10|max:15|unique:users,phone',
            'id_number' => 'required|string|min:10|max:255|unique:users,id_number',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:admin,doctor,patient',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'id_number' => $validated['id_number'],
            'password' => Hash::make($validated['password']),
            // لو مفيش role جاية من الفورم خليه patient افتراضي
            'role' => $validated['role'] ?? 'patient',
        ]);

        // لو اللي عامل إنشاء يوزر هو أدمن ومسجل دخوله دلوقتي
        if (Auth::check() && Auth::user()->role === 'admin') {
            // مهم: ما تعملش Auth::login($user) عشان ما تبدلش جلسة الأدمن باليوزر الجديد
            return redirect()
                ->route('users.show', $user->id)
                ->with('success', 'User created successfully');
        }

        // لو حد بيسجل أول مرة (مش أدمن)
        Auth::login($user);

        return redirect()
            ->route('home')
            ->with('success', 'Registration successful');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('/');
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);


        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'id_number' => $request->id_number,
            'role' => $request->role,
        ];

        // لو المستخدم كتب باسورد جديد
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('id_number', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);
        return view('admin.users.show', compact('users', 'search'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
    public function show($id)
    {
        $users = User::All();
        return view('admin.users.show', compact('users'));
    }

}
