<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nama_usaha' => 'required|string|max:255',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'umkm',
            'status_akun' => 'aktif',
        ]);

        \App\Models\Umkm::create([
            'id_user' => $user->id,
            'nama_usaha' => $request->nama_usaha,
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('umkm.dashboard');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = \Illuminate\Support\Facades\Auth::user()->role;
            if ($role === 'petugas') {
                return redirect()->route('petugas.dashboard');
            } elseif ($role === 'pimpinan') {
                return redirect()->route('pimpinan.dashboard');
            } elseif ($role === 'umkm') {
                return redirect()->route('umkm.dashboard');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'login' => 'Email/Username atau kata sandi salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function dashboardPetugas()
    {
        return "<h1>Dashboard Petugas</h1><form method='POST' action='".route('logout')."'>".csrf_field()."<button type='submit'>Logout</button></form>";
    }

    public function dashboardPimpinan()
    {
        return "<h1>Dashboard Pimpinan</h1><form method='POST' action='".route('logout')."'>".csrf_field()."<button type='submit'>Logout</button></form>";
    }

    public function dashboardUmkm()
    {
        return "<h1>Dashboard UMKM</h1><form method='POST' action='".route('logout')."'>".csrf_field()."<button type='submit'>Logout</button></form>";
    }
}
