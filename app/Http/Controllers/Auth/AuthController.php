<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return back()
                    ->withErrors(['email' => 'Email atau password tidak valid.'])
                    ->onlyInput('email');
            }

            $user = Auth::user();

            if (!$user) {
                throw new \Exception('Auth::user() returned null');
            }

            if (!$user->status) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi Admin Finance.'
                ]);
            }

            $roleName = $user->role?->nama_role;

            if (!$roleName) {
                throw new \Exception('User role tidak ditemukan');
            }

            if ($roleName === 'Admin Finance') {
                $redirectPath = route('dashboard.index');
            } elseif ($roleName === 'Vendor') {
                $redirectPath = route('scan.index');
            } elseif ($roleName === 'View Only') {
                $redirectPath = route('dashboard.index');
            } else {
                throw new \Exception('Role tidak dikenali: ' . $roleName);
            }

            $request->session()->regenerate();

            return redirect($redirectPath);
        } catch (\Throwable $e) {
            return response(
                "LOGIN ERROR\n\n" .
                "CLASS: " . get_class($e) . "\n" .
                "MESSAGE: " . $e->getMessage() . "\n" .
                "FILE: " . $e->getFile() . "\n" .
                "LINE: " . $e->getLine(),
                500,
                ['Content-Type' => 'text/plain']
            );
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}