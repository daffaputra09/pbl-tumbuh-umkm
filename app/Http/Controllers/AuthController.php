<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'business_name' => ['required', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_BUSINESS_OWNER,
                'is_active' => true,
            ]);

            Business::create([
                'user_id' => $user->id,
                'created_by' => $user->id,
                'business_name' => $validated['business_name'],
                'owner_name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route($user->homeRouteName());
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
            $user = Auth::user();

            if (! $user instanceof User || ! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'login' => EnsureUserIsActive::MESSAGE,
                ])->onlyInput('login');
            }

            $request->session()->regenerate();

            return redirect()->route($user->homeRouteName());
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
        return redirect()->route('dashboard');
    }

    public function dashboardPimpinan()
    {
        return redirect()->route('dashboard');
    }

    public function dashboardUmkm()
    {
        return "<h1>Dashboard UMKM</h1><form method='POST' action='".route('logout')."'>".csrf_field()."<button type='submit'>Logout</button></form>";
    }
}
