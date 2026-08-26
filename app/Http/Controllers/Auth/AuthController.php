<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Support\RoleName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

            Log::info('LOGIN STEP 1', [
                'email' => $request->email,
            ]);

            if (! Auth::attempt($credentials)) {
                Log::info('LOGIN STEP 2: AUTH FAILED');

                return back()
                    ->withErrors([
                        'email' => 'Email atau password tidak valid.'
                    ])
                    ->onlyInput('email');
            }

            Log::info('LOGIN STEP 2: AUTH SUCCESS');

            $user = Auth::user();

            Log::info('LOGIN STEP 3: USER LOADED', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id,
            ]);

            if (! $user->status) {
                Log::info('LOGIN STEP 4: USER INACTIVE');

                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi Admin Finance.'
                ]);
            }

            Log::info('LOGIN STEP 4: USER ACTIVE');

            $request->session()->regenerate();

            Log::info('LOGIN STEP 5: SESSION REGENERATED');

            $roleName = $user->role?->nama_role;

            Log::info('LOGIN STEP 6: ROLE LOADED', [
                'role' => $roleName,
            ]);

            $path = $this->redirectPathForRole($roleName);

            Log::info('LOGIN STEP 7: REDIRECT PATH', [
                'path' => $path,
            ]);

            return redirect($path);

        } catch (\Throwable $e) {
            Log::error('LOGIN ERROR', [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
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