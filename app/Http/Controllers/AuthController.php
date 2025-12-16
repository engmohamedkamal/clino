<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(){
        return view('register');
    }
     public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        $field = is_numeric($credentials['login']) ? 'phone' : 'name';

        $user = User::where($field, $credentials['login'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('login'))
                ->with('error', 'Invalid credentials');
        }

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Logging successful');
    }
     public function store(Request $request)
    {
        $validated = $request->validate([
            'register_for' => 'required|in:self,other',
            'name'         => 'required|string|min:3|max:255',
            'phone'        => 'required|string|min:10|max:15|unique:users,phone',
            'id_number'    => 'required|string|min:14|max:14|unique:users,id_number',
            'password'     => 'required|string|min:8',
            'role'     => 'nullable|string|min:8|in:admin,doctor,patient',
        ]);

    
        $user = User::create([
            'name'         => $validated['name'],
            'phone'        => $validated['phone'],
            'id_number'    => $validated['id_number'],
            'register_for' => $validated['register_for'],
            'password'     => Hash::make($validated['password']),
        ]);
        Auth::login($user);
        return redirect()->route('home')->with('success', 'Registration successful');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('/');
    }
}
