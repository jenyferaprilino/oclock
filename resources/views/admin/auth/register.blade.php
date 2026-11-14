@extends('layouts.admin')

@section('title', 'Tambah Administrator Baru')
@section('header_title', 'Pembuatan Akun Administrator')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="font-serif text-xl font-bold text-[#242321]">Formulir Administrator Baru</h3>
            <p class="text-xs text-[#777168] mt-1">
                Akun yang dibuat di sini otomatis memiliki role Administrator (ADM-01). Hanya dapat diakses oleh Admin yang sedang terotorisasi.
            </p>
        </div>
        <span class="text-xs uppercase tracking-wider font-semibold px-2.5 py-1 rounded bg-[#242321] text-white">
            Super / Admin
        </span>
    </div>

    <div class="bg-white p-8 rounded-2xl border border-[#E6E1D7] shadow-sm">
        <form method="POST" action="{{ route('admin.register.post') }}" class="space-y-5">
            @csrf

            <!-- Name Input -->
            <div>
                <label for="name" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Nama Lengkap Administrator <span class="text-rose-500">*</span>
                </label>
                <div class="mt-2">
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                           placeholder="Contoh: Manager Operasional"
                           class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm text-[#242321] focus:ring-1 focus:ring-[#C2A36B] @error('name') border-rose-400 @enderror">
                </div>
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Alamat Email Administrator <span class="text-rose-500">*</span>
                </label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           placeholder="admin.ops@oclock.test"
                           class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm text-[#242321] focus:ring-1 focus:ring-[#C2A36B] @error('email') border-rose-400 @enderror">
                </div>
                @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Phone Input -->
            <div>
                <label for="phone" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Nomor Kontak / WhatsApp <span class="text-[#777168] lowercase font-normal">(opsional)</span>
                </label>
                <div class="mt-2">
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                           placeholder="081234567890"
                           class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm text-[#242321] focus:ring-1 focus:ring-[#C2A36B] @error('phone') border-rose-400 @enderror">
                </div>
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Passwords -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" required
                               placeholder="Min. 8 karakter"
                               class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm text-[#242321] focus:ring-1 focus:ring-[#C2A36B] @error('password') border-rose-400 @enderror">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               placeholder="Ulangi kata sandi"
                               class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm text-[#242321] focus:ring-1 focus:ring-[#C2A36B]">
                    </div>
                </div>
            </div>
            @error('password') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

            <!-- Submit Buttons -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E6E1D7]">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-5 py-2.5 rounded-xl border border-[#E6E1D7] text-xs uppercase tracking-wider font-semibold text-[#777168] hover:text-[#242321] transition-all">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white text-xs uppercase tracking-wider font-semibold transition-all cursor-pointer">
                    Buat Akun Administrator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
