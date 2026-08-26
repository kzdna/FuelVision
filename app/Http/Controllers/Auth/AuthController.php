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
            Log::info('LOGIN STEP 1: request received');

            $credentials = $request->only('email', 'password');

            Log::info('LOGIN STEP 2: credentials received', [
                'email' => $credentials['email'] ?? null,
            ]);

            $attempt = Auth::attempt($credentials);

            Log::info('LOGIN STEP 3: Auth attempt completed', [
                'result' => $attempt,
            ]);

            if (! $attempt) {
                Log::warning('LOGIN STEP 4: authentication failed');

                return back()
                    ->withErrors(['email' => 'Email atau password tidak valid.'])
                    ->onlyInput('email');
            }

            Log::info('LOGIN STEP 5: authentication successful');

            $user = Auth::user();

            Log::info('LOGIN STEP 6: user retrieved', [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'status' => $user?->status,
            ]);

            if (! $user) {
                throw new \RuntimeException('Auth::user() returned null after successful authentication.');
            }

            if (! $user->status) {
                Log::warning('LOGIN STEP 7: user inactive');

                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi Admin Finance.'
                ]);
            }

            Log::info('LOGIN STEP 8: regenerating session');

            $request->session()->regenerate();

            Log::info('LOGIN STEP 9: session regenerated');

            $roleName = $user->role?->nama_role;

            Log::info('LOGIN STEP 10: role retrieved', [
                'role' => $roleName,
            ]);

            $redirectPath = $this->redirectPathForRole($roleName);

            Log::info('LOGIN STEP 11: redirect path determined', [
                'path' => $redirectPath,
            ]);

            return redirect($redirectPath);

        } catch (\Throwable $e) {
            Log::error('LOGIN ERROR', [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'LOGIN ERROR: ' . $e->getMessage());
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