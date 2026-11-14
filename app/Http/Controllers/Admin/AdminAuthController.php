<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan formulir login khusus Administrator (ADM-01).
     */
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Proses autentikasi khusus Administrator dengan verifikasi role dan rate limiting.
     * Akun Customer ditolak dengan pesan aman.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        // Customer tidak boleh login via endpoint Admin
        // Gunakan pesan generik identik untuk mencegah user enumeration
        if ($user && ! $user->isAdmin()) {
            RateLimiter::hit($this->throttleKey($request), 60);

            throw ValidationException::withMessages([
                'email' => __('Email atau kata sandi tidak sesuai.'),
            ]);
        }

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey($request), 60);

            throw ValidationException::withMessages([
                'email' => __('Email atau kata sandi tidak sesuai.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Tampilkan formulir registrasi admin baru (Hanya untuk Admin yang sudah login).
     */
    public function showRegister(): View
    {
        return view('admin.auth.register');
    }

    /**
     * Proses pembuatan akun administrator baru.
     * Dilakukan oleh Admin yang sedang login tanpa mengubah sesi aktifnya.
     */
    public function register(CreateAdminRequest $request): RedirectResponse
    {
        $newAdmin = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'),
            'role' => 'admin', // Dikunci mutlak oleh backend
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', "Akun Administrator baru '{$newAdmin->name}' ({$newAdmin->email}) berhasil dibuat.");
    }

    /**
     * Rate limiter helper untuk login admin.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('Terlalu banyak percobaan masuk Admin. Silakan coba lagi dalam :seconds detik.', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate('admin_auth|'.Str::lower($request->input('email')).'|'.$request->ip());
    }
}
