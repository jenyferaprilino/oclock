<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir registrasi publik khusus customer (CUS-04).
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran customer baru.
     * Mengunci role mutlak menjadi 'customer' (BR-01 & BR-02).
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Selamat datang di O\'Clock! Akun pelanggan Anda berhasil dibuat.');
    }

    /**
     * Tampilkan formulir login khusus customer (CUS-05).
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi customer dengan proteksi role dan rate limiting.
     * Admin ditolak pada login ini demi pemisahan konteks.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request, 'customer');

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Cari user terlebih dahulu untuk verifikasi role
        $user = User::where('email', $credentials['email'])->first();

        // Tolak jika user terdaftar tetapi rolenya bukan customer
        // Gunakan pesan generik identik untuk mencegah user enumeration
        if ($user && ! $user->isCustomer()) {
            RateLimiter::hit($this->throttleKey($request, 'customer'), 60);

            throw ValidationException::withMessages([
                'email' => __('Email atau kata sandi tidak sesuai.'),
            ]);
        }

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey($request, 'customer'), 60);

            throw ValidationException::withMessages([
                'email' => __('Email atau kata sandi tidak sesuai.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request, 'customer'));
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Logout pengguna dan hancurkan session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $isAdmin = Auth::user() && Auth::user()->isAdmin();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isAdmin) {
            return redirect()->route('admin.login')
                ->with('info', 'Anda telah berhasil keluar dari sesi Administrator.');
        }

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari akun.');
    }

    /**
     * Rate limiter helper.
     */
    protected function ensureIsNotRateLimited(Request $request, string $prefix = 'auth'): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request, $prefix), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request, $prefix));

        throw ValidationException::withMessages([
            'email' => trans('Terlalu banyak percobaan masuk. Silakan coba lagi dalam :seconds detik.', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    protected function throttleKey(Request $request, string $prefix = 'auth'): string
    {
        return Str::transliterate($prefix.'|'.Str::lower($request->input('email')).'|'.$request->ip());
    }
}
