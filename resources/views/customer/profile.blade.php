@extends('layouts.app')

@section('title', "Profil Saya — O'Clock")

@section('content')
<div class="bg-[#F7F4EC] min-h-screen py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ─── Breadcrumb Navigation ─────────────────────────────────────── -->
        <nav class="flex items-center gap-2 text-xs text-[#777168] mb-6 font-light">
            <a href="{{ route('home') }}" class="hover:text-[#242321] transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-[#242321] font-medium">Profil Pelanggan</span>
        </nav>

        <!-- ─── Header Customer Profile ───────────────────────────────────── -->
        <div class="bg-white rounded-3xl border border-[#E5DCCE] p-6 sm:p-8 mb-8 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-[#242321] text-[#A88B5A] font-serif text-2xl font-bold flex items-center justify-center border-2 border-[#A88B5A]/40 shadow-xs">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#242321]">
                            {{ $user->name }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#A88B5A]/15 text-[#A88B5A] uppercase tracking-wider">
                            Customer
                        </span>
                    </div>
                    <p class="text-xs text-[#777168] mt-0.5 font-light">
                        {{ $user->email }} • Terdaftar sejak {{ $user->created_at ? $user->created_at->format('d M Y') : '2026' }}
                    </p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('customer.orders.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-[#242321] text-[#242321] hover:text-white border border-[#E5DCCE] hover:border-[#242321] text-xs font-semibold uppercase tracking-wider transition-all shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-[#A88B5A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>Pesanan Saya</span>
                </a>
                <a href="{{ route('catalog.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#F7F4EC] hover:bg-[#242321] text-[#242321] hover:text-white border border-[#E5DCCE] hover:border-[#242321] text-xs font-semibold uppercase tracking-wider transition-all shadow-2xs">
                    <span>Jelajahi Jam Tangan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- ─── Profile Form Card ─────────────────────────────────────────── -->
        <div class="bg-white rounded-3xl border border-[#E5DCCE] p-6 sm:p-10 shadow-xs">
            <div class="border-b border-[#E5DCCE]/70 pb-5 mb-6">
                <h2 class="font-serif text-xl font-bold text-[#242321]">Informasi Akun Belanja</h2>
                <p class="text-xs text-[#777168] mt-1 font-light">Perbarui data kontak Anda untuk kemudahan proses pengiriman pada pesanan mendatang.</p>
            </div>

            <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Email Display (Read-Only) -->
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Alamat Email Akun
                    </label>
                    <div class="mt-2">
                        <input type="email" value="{{ $user->email }}" disabled
                            class="block w-full px-4 py-3 bg-[#F7F4EC]/70 border border-[#E5DCCE] rounded-xl text-[#777168] text-sm cursor-not-allowed">
                    </div>
                    <p class="mt-1.5 text-xs text-[#777168] font-light">
                        Alamat email digunakan sebagai tanda pengenal akun dan tidak dapat diubah demi keamanan.
                    </p>
                </div>

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Nama Lengkap
                    </label>
                    <div class="mt-2">
                        <input id="name" name="name" type="text" required
                            value="{{ old('name', $user->name) }}"
                            class="block w-full px-4 py-3 bg-[#F7F4EC]/40 border border-[#E5DCCE] rounded-xl text-[#242321] focus:outline-none focus:ring-2 focus:ring-[#A88B5A]/50 focus:border-[#A88B5A] transition-all text-sm @error('name') border-red-400 @enderror">
                    </div>
                    @error('name')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Input -->
                <div>
                    <label for="phone" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                        Nomor Telepon / WhatsApp
                    </label>
                    <div class="mt-2">
                        <input id="phone" name="phone" type="tel"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="Contoh: 081234567890"
                            class="block w-full px-4 py-3 bg-[#F7F4EC]/40 border border-[#E5DCCE] rounded-xl text-[#242321] focus:outline-none focus:ring-2 focus:ring-[#A88B5A]/50 focus:border-[#A88B5A] transition-all text-sm @error('phone') border-red-400 @enderror">
                    </div>
                    @error('phone')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E5DCCE]/70">
                    <button type="submit"
                        class="py-3 px-8 rounded-full bg-[#242321] hover:bg-[#A88B5A] text-white font-semibold text-xs uppercase tracking-wider transition-all duration-300 shadow-2xs hover:shadow-xs cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
