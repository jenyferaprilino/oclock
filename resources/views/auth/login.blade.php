@extends('layouts.auth')

@section('title', "Masuk ke Akun Anda — O'Clock")

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <div class="text-center">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold tracking-tight text-[#242321]">
            Selamat Datang Kembali
        </h1>
        <p class="mt-2 text-sm text-[#777168]">
            Masuk untuk mengakses koleksi dan riwayat pesanan Anda
        </p>
    </div>

    <div class="mt-8 bg-white py-10 px-6 sm:px-10 shadow-sm border border-[#E6E1D7] rounded-2xl">
        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Alamat Email Pelanggan
                </label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" autocomplete="email" required
                        value="{{ old('email') }}"
                        class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm @error('email') border-red-400 focus:ring-red-200 @enderror"
                        placeholder="pelanggan@email.com">
                </div>
                @error('email')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Kata Sandi
                    </label>
                </div>
                <div class="mt-2">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm @error('password') border-red-400 focus:ring-red-200 @enderror"
                        placeholder="••••••••">
                </div>
                @error('password')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E6E1D7] text-[#C2A36B] focus:ring-[#C2A36B]/40">
                    <span class="text-xs text-[#777168]">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white font-medium text-sm tracking-wide transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer">
                    Masuk ke Akun
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-[#E6E1D7] text-center">
            <p class="text-xs text-[#777168]">
                Belum memiliki akun O'Clock?
                <a href="{{ route('register') }}" class="font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                    Daftar sekarang
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
