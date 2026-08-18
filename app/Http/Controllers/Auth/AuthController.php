<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Support\RoleName;
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
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak valid.'])
                ->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->status) {
            Auth::logout();

            return back()->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi Admin Finance.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathForRole($user->role?->nama_role));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectPathForRole(?string $roleName): string
    {
        return match ($roleName) {
            RoleName::VENDOR => route('scan.index'),
            RoleName::ADMIN_FINANCE, RoleName::VIEW_ONLY => route('dashboard.index'),
            default => route('login'),
        };
    }
}
