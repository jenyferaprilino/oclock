@extends('layouts.app')

@section('title', "O'Clock — Timeless Style, Every Moment | Butik Jam Tangan")

@section('content')
<!-- ─── Hero Section (Quiet Luxury) ───────────────────────────────────────── -->
<section class="relative overflow-hidden bg-gradient-to-b from-[#F7F4EC] via-white to-[#F7F4EC] border-b border-[#E5DCCE] py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Editorial Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <!-- Tagline Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-[#E5DCCE] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#A88B5A]"></span>
                    <span class="text-xs font-semibold uppercase tracking-widest text-[#777168]">
                        Butik Jam Tangan Terkurasi • Pengiriman Kota Bengkulu
                    </span>
                </div>

                <!-- Main Editorial Headline -->
                <h1 class="font-serif text-4xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-[#242321] leading-[1.08]">
                    Timeless Style,<br>
                    <span class="italic font-normal text-[#A88B5A]">Every Moment.</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-[#777168] max-w-2xl mx-auto lg:mx-0 font-light leading-relaxed">
                    Setiap detik mencerminkan karakter dan perjalanan Anda. O'Clock mempersembahkan kurasi jam tangan dari brand internasional ikonik, koleksi prestisius Alexandre Christie, hingga mahakarya pengrajin lokal Indonesia.
                </p>

                <!-- CTA Actions -->
                <div class="pt-3 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('catalog.index') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-[#242321] hover:bg-[#A88B5A] text-white text-xs uppercase tracking-widest font-semibold transition-all shadow-xs">
                        <span>Jelajahi Koleksi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="#categories"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-white hover:bg-[#F7F4EC] text-[#242321] border border-[#E5DCCE] text-xs uppercase tracking-widest font-semibold transition-all">
                        Pilih Kategori
                    </a>
                </div>

                <!-- Verified Brand Badges -->
                <div class="pt-8 border-t border-[#E5DCCE]/70">
                    <span class="block text-[10px] uppercase tracking-widest font-medium text-[#777168] mb-3">
                        Brand Pilihan dalam Katalog
                    </span>
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 text-xs text-[#242321] font-medium">
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">Alexandre Christie</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">Seiko</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">Citizen</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">Casio</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">Orient</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">Timex</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">EIGER</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">PALA Nusantara</span>
                        <span class="px-3 py-1 bg-white rounded-full border border-[#E5DCCE]">Woodka</span>
                    </div>
                </div>
            </div>

            <!-- Right Showcase Card (Featured Watch Preview) -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative w-full max-w-md p-6 sm:p-8 bg-white rounded-3xl border border-[#E5DCCE] shadow-sm">
                    <div class="aspect-square bg-[#F7F4EC] rounded-2xl overflow-hidden border border-[#E5DCCE]/80 p-6">
                        <x-product-photo :product="$showcaseProduct" brand="Alexandre Christie" />
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] uppercase tracking-widest font-semibold text-[#A88B5A]">Koleksi Unggulan</span>
                            <h2 class="font-serif text-xl font-bold text-[#242321]">Alexandre Christie Open-Heart</h2>
                            <p class="text-xs text-[#777168] mt-0.5">AC 9601 MAR Mechanical Automatic</p>
                        </div>
                        <a href="{{ route('catalog.show', 'alexandre-christie-ac-9601-mar-open-heart') }}"
                           class="w-10 h-10 rounded-full bg-[#242321] text-white flex items-center justify-center hover:bg-[#A88B5A] transition-colors shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── Categories Showcase ────────────────────────────────────────────────── -->
<section id="categories" class="py-16 sm:py-20 bg-white border-b border-[#E5DCCE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-[#A88B5A]">Klasifikasi Gaya</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#242321] mt-1">Kategori Jam Tangan</h2>
                <p class="text-sm text-[#777168] mt-1 font-light">Pilih gaya jam tangan yang sesuai dengan kebutuhan dan karakter momen Anda.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-[#777168] hover:text-[#A88B5A] transition-colors">
                <span>Lihat Seluruh Koleksi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @forelse($categories as $category)
                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                   class="group p-5 bg-[#F7F4EC]/60 hover:bg-white rounded-2xl border border-[#E5DCCE] hover:border-[#A88B5A] hover:shadow-xs transition-all duration-300 text-center flex flex-col justify-between">
                    <div class="w-12 h-12 mx-auto rounded-full bg-white group-hover:bg-[#A88B5A]/10 border border-[#E5DCCE] group-hover:border-[#A88B5A] flex items-center justify-center transition-colors">
                        <svg class="w-6 h-6 text-[#242321] group-hover:text-[#A88B5A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mt-4">
                        <h3 class="font-serif text-base font-semibold text-[#242321] group-hover:text-[#A88B5A] transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-[11px] text-[#777168] mt-0.5 font-light">
                            {{ $category->products_count }} Jam Tangan
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-8 text-sm text-[#777168]">
                    Kategori belum tersedia saat ini.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- ─── Featured Products Section ─────────────────────────────────────────── -->
<section class="py-16 sm:py-24 bg-[#F7F4EC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-[#A88B5A]">Kurasi Pilihan</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#242321] mt-1">Koleksi Jam Tangan Unggulan</h2>
                <p class="text-sm text-[#777168] mt-1 font-light">Kurasi produk jam tangan aktif terpopuler dengan material presisi dan identitas desain otentik.</p>
            </div>
            <a href="{{ route('catalog.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#242321] hover:bg-[#A88B5A] text-white text-xs uppercase tracking-wider font-semibold transition-all shadow-xs">
                <span>Semua Koleksi (Katalog)</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @forelse($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-[#E5DCCE] p-12 text-center">
                    <svg class="w-12 h-12 text-[#777168]/40 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="font-serif text-lg font-bold text-[#242321]">Koleksi Jam Tangan Sedang Disiapkan</h3>
                    <p class="text-xs text-[#777168] mt-1">Silakan kunjungi kembali nanti untuk melihat rilis jam tangan terbaru.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- ─── Boutique Values / Pillars ─────────────────────────────────────────── -->
<section class="py-16 bg-white border-t border-[#E5DCCE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pillar 1 -->
            <div class="p-6 rounded-2xl bg-[#F7F4EC]/60 border border-[#E5DCCE] flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-white border border-[#E5DCCE] flex items-center justify-center shrink-0 text-[#A88B5A]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-serif text-base font-bold text-[#242321]">Kurasi Brand Terpercaya</h4>
                    <p class="text-xs text-[#777168] mt-1 leading-relaxed font-light">
                        Menghadirkan model nyata dari Seiko, Citizen, Casio, Orient, Timex, Alexandre Christie, hingga brand artisan Indonesia PALA, Woodka, dan EIGER.
                    </p>
                </div>
            </div>

            <!-- Pillar 2 -->
            <div class="p-6 rounded-2xl bg-[#F7F4EC]/60 border border-[#E5DCCE] flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-white border border-[#E5DCCE] flex items-center justify-center shrink-0 text-[#A88B5A]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-serif text-base font-bold text-[#242321]">Pengiriman Aman dari Bengkulu</h4>
                    <p class="text-xs text-[#777168] mt-1 leading-relaxed font-light">
                        Titik kirim resmi Kota Bengkulu dengan pengemasan protektif boks eksklusif dan jaminan keamanan sampai ke alamat tujuan.
                    </p>
                </div>
            </div>

            <!-- Pillar 3 -->
            <div class="p-6 rounded-2xl bg-[#F7F4EC]/60 border border-[#E5DCCE] flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-white border border-[#E5DCCE] flex items-center justify-center shrink-0 text-[#A88B5A]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-serif text-base font-bold text-[#242321]">Transparansi Horologi</h4>
                    <p class="text-xs text-[#777168] mt-1 leading-relaxed font-light">
                        Setiap produk dilengkapi spesifikasi detail diameter case, tipe mesin, material strap, dan ketahanan air yang akurat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
