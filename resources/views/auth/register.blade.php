@extends('layouts.auth')

@section('title', "Pendaftaran Akun Baru — O'Clock")

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-lg">
    <div class="text-center">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold tracking-tight text-[#242321]">
            Bergabung dengan O'Clock
        </h1>
        <p class="mt-2 text-sm text-[#777168]">
            Daftar untuk menikmati pengalaman berbelanja jam tangan mewah & elegan
        </p>
    </div>

    <div class="mt-8 bg-white py-10 px-6 sm:px-10 shadow-sm border border-[#E6E1D7] rounded-2xl">
        <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
            @csrf

            <!-- Name Input -->
            <div>
                <label for="name" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Nama Lengkap
                </label>
                <div class="mt-2">
                    <input id="name" name="name" type="text" autocomplete="name" required
                        value="{{ old('name') }}"
                        class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm @error('name') border-red-400 focus:ring-red-200 @enderror"
                        placeholder="Contoh: Raden Wijaya">
                </div>
                @error('name')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Alamat Email
                </label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" autocomplete="email" required
                        value="{{ old('email') }}"
                        class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm @error('email') border-red-400 focus:ring-red-200 @enderror"
                        placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Input -->
            <div>
                <label for="phone" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Nomor Telepon / WhatsApp <span class="text-[#777168] lowercase font-normal">(opsional)</span>
                </label>
                <div class="mt-2">
                    <input id="phone" name="phone" type="tel" autocomplete="tel"
                        value="{{ old('phone') }}"
                        class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm @error('phone') border-red-400 focus:ring-red-200 @enderror"
                        placeholder="081234567890">
                </div>
                @error('phone')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Inputs (Grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Kata Sandi
                    </label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm @error('password') border-red-400 focus:ring-red-200 @enderror"
                            placeholder="Min. 8 karakter">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Konfirmasi Sandi
                    </label>
                    <div class="mt-2">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] placeholder-[#777168]/50 focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all text-sm"
                            placeholder="Ulangi kata sandi">
                    </div>
                </div>
            </div>
            @error('password')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-[#C2A36B] hover:bg-[#AF8F55] text-white font-medium text-sm tracking-wide transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer">
                    Buat Akun Customer
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-[#E6E1D7] text-center">
            <p class="text-xs text-[#777168]">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-semibold text-[#242321] hover:text-[#C2A36B] transition-colors">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
