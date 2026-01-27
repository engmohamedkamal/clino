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
            return back()->withErrors('Select User / Users First to Delete');
        }
        $ids = array_values(array_diff($ids, [auth()->id()]));

        User::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Users deleted successfully.');
    }
    public function register()
    {
        $doctors = User::where('role','doctor')->get();
        return view('dashboard.users.add',compact('doctors'));
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
  'name'      => 'required|string|min:3|max:255',
  'phone'     => 'required|string|min:10|max:15|unique:users,phone',
  'id_number' => 'required|string|min:10|max:255|unique:users,id_number',
  'password'  => 'required|string|min:8|confirmed',

  'role'      => 'nullable|string|in:admin,doctor,patient,secretary',

  'doctor_id' => [
    'nullable',
    // ✅ لازم يبقى دكتور فعلاً
    \Illuminate\Validation\Rule::exists('users', 'id')->where(function ($q) {
      $q->where('role', 'doctor');
    }),

    // ✅ لو سكرتيرة لازم تختار دكتور
    function ($attr, $value, $fail) use ($request) {
      if ($request->input('role') === 'secretary' && empty($value)) {
        $fail('Doctor is required for secretary role.');
      }
    },

    // ✅ لو مش سكرتيرة ممنوع تبعت doctor_id
    function ($attr, $value, $fail) use ($request) {
      if ($request->input('role') !== 'secretary' && !empty($value)) {
        $fail('Doctor can only be assigned when role is secretary.');
      }
    },
  ],
]);


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
        if (Auth::check() && Auth::user()->role === 'secretary') {
            return redirect()
                ->back()
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

        return redirect('/');
    }
    public function show($id)
    {
        $users = User::latest()->paginate(10);
        return view('dashboard.users.view', compact('users'));
    }
    public function edit($id)
    {
         $doctors = User::where('role','doctor')->get();
        $user = User::findOrFail($id);
        return view('dashboard.users.edit', compact('user','doctors'));
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
            'role' => ['nullable', 'string', Rule::in(['admin', 'doctor', 'patient','secretary'])],

            // ✅ password optional
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            
  'doctor_id' => [
    'nullable',
    // ✅ لازم يبقى دكتور فعلاً
    \Illuminate\Validation\Rule::exists('users', 'id')->where(function ($q) {
      $q->where('role', 'doctor');
    }),

    // ✅ لو سكرتيرة لازم تختار دكتور
    function ($attr, $value, $fail) use ($request) {
      if ($request->input('role') === 'secretary' && empty($value)) {
        $fail('Doctor is required for secretary role.');
      }
    },

    // ✅ لو مش سكرتيرة ممنوع تبعت doctor_id
    function ($attr, $value, $fail) use ($request) {
      if ($request->input('role') !== 'secretary' && !empty($value)) {
        $fail('Doctor can only be assigned when role is secretary.');
      }
    },
  ],
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
