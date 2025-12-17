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
        'email'    => ['required', 'email'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        return back()
            ->withInput($request->only('email'))
           ->withErrors(['email' => __('auth.failed')]);
    }

    $request->session()->regenerate();

    return redirect()->route('home')->with('success', 'Logging successful');
}
     public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|min:3|max:255',
            'phone'        => 'required|string|min:10|max:15|unique:users,phone',
            'email'    => 'required|string|min:10|max:255|unique:users,email',
            'password'     => 'required|string|min:8',
            'role'     => 'nullable|string|min:8|in:admin,doctor,patient',
        ]);

    
        $user = User::create([
            'name'         => $validated['name'],
            'phone'        => $validated['phone'],
            'email'    => $validated['email'],
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
