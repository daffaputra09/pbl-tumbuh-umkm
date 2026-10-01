<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);

        Umkm::create([
            'id_user' => $user->id,
            'nama_usaha' => $request->nama_usaha,
        ]);

        Auth::login($user);

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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;
            if ($role === User::ROLE_OFFICER) {
                return redirect()->route('petugas.dashboard');
            } elseif ($role === User::ROLE_VILLAGE_HEAD) {
                return redirect()->route('pimpinan.dashboard');
            } elseif ($role === User::ROLE_BUSINESS_OWNER) {
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
        Auth::logout();
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
