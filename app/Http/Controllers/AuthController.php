<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('id_number', 'like', "%{$q}%")
                        ->orWhere('role', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.users.view', compact('users'));
    }
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->withErrors('اختار يوزر/يوزرز الأول عشان تعمل Delete');
        }
        $ids = array_values(array_diff($ids, [auth()->id()]));

        User::whereIn('id', $ids)->delete();

        return redirect()->route('users.index')->with('success', 'Users deleted successfully.');
    }
    public function register()
    {
        return view('dashboard.users.add');
    }
    public function userRegister()
    {
        return view('register');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string', 'regex:/^[0-9]+$/', 'digits:11'],
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

        // لو اللي بيعمل register مش أدمن، خليه patient افتراضي
        // لو أدمن هو اللي بيضيف، يقدر يختار role من الفورم
        $role = $validated['role'] ?? (Auth::check() && Auth::user()->role === 'admin' ? 'patient' : 'patient');

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'id_number' => $validated['id_number'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ]);

        // ✅ لو Admin بيضيف مستخدم: ما نعملش login للمستخدم الجديد
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully');
        }

        // ✅ لو Register طبيعي: login للمستخدم الجديد
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

        return redirect('/'); // ✅ بدل route('/')
    }
    public function show($id)
    {
        $users = User::latest()->paginate(10);
        return view('dashboard.users.view', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.edit', compact('user'));
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'id_number' => [
                'required',
                'string',
                'min:10',
                'max:255',
                Rule::unique('users', 'id_number')->ignore($user->id),
            ],
            'role' => ['required', 'string', Rule::in(['admin', 'doctor', 'patient'])],

            // ✅ password optional
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'id_number' => $validated['id_number'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
