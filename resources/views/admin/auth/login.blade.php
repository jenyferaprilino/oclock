@extends('layouts.admin_auth')

@section('title', "O'Clock Admin Panel — Masuk Administrator")

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <div class="text-center">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold tracking-wider uppercase bg-[#C2A36B]/15 text-[#C2A36B] mb-3">
            Internal Area
        </span>
        <h1 class="font-serif text-3xl font-bold tracking-tight text-[#242321]">
            O'Clock Admin Panel
        </h1>
        <p class="mt-2 text-xs text-[#777168]">
            Masuk dengan kredensial Administrator untuk mengelola operasional toko
        </p>
    </div>

    <div class="mt-8 bg-white py-10 px-6 sm:px-10 shadow-sm border border-[#E6E1D7] rounded-2xl">
        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Email Administrator
                </label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" autocomplete="email" required
                        value="{{ old('email') }}"
                        class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm @error('email') border-red-400 focus:ring-red-200 @enderror"
                        placeholder="admin@oclock.test">
                </div>
                @error('email')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Kata Sandi
                </label>
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
                    <span class="text-xs text-[#777168]">Ingat sesi Administrator</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white font-medium text-xs uppercase tracking-wider transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer">
                    Masuk ke Panel Admin
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-[#E6E1D7] text-center">
            <p class="text-xs text-[#777168]">
                Bukan Administrator?
                <a href="{{ route('login') }}" class="font-semibold text-[#242321] hover:text-[#C2A36B] transition-colors">
                    Menuju Login Pelanggan &rarr;
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
