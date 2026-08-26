<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        // Cek email dan password
        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password tidak valid.'
                ])
                ->onlyInput('email');
        }

        // Ambil user yang berhasil login
        $user = Auth::user();

        // Regenerate session setelah login berhasil
        $request->session()->regenerate();

        // Cek status akun
        if (!$user->status) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi Admin Finance.'
                ]);
        }

        // Ambil nama role
        $roleName = $user->role?->nama_role;

        // Kalau role tidak ditemukan
        if (!$roleName) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Role akun tidak ditemukan. Hubungi Admin.'
                ]);
        }

        // Redirect berdasarkan role
        if ($roleName === 'Admin Finance') {
            return redirect()->route('dashboard.index');
        }

        if ($roleName === 'Vendor') {
            return redirect()->route('scan.index');
        }

        if ($roleName === 'View Only') {
            return redirect()->route('dashboard.index');
        }

        // Role tidak dikenali
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Role akun tidak dikenali. Hubungi Admin.'
            ]);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}