@extends('layouts.app')

@section('title', "{$product->name} — O'Clock")

@section('content')
<div class="bg-[#F7F4EC] min-h-screen py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ─── Breadcrumb Navigation ─────────────────────────────────────── -->
        <nav class="flex items-center gap-2 text-xs text-[#777168] mb-8 overflow-x-auto scrollbar-none pb-1 font-light">
            <a href="{{ route('home') }}" class="hover:text-[#242321] transition-colors shrink-0">Beranda</a>
            <span>/</span>
            <a href="{{ route('catalog.index') }}" class="hover:text-[#242321] transition-colors shrink-0">Koleksi Jam Tangan</a>
            @if($product->category)
                <span>/</span>
                <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}"
                   class="hover:text-[#242321] transition-colors shrink-0">
                    {{ $product->category->name }}
                </a>
            @endif
            <span>/</span>
            <span class="text-[#242321] font-medium truncate shrink-0 max-w-xs sm:max-w-md">{{ $product->name }}</span>
        </nav>

        <!-- ─── Main Product Detail Container ─────────────────────────────── -->
        <div class="bg-white rounded-3xl border border-[#E5DCCE] overflow-hidden shadow-xs p-6 sm:p-10 mb-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                <!-- Left: Product Showcase Gallery -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="relative aspect-square bg-[#F7F4EC] rounded-2xl border border-[#E5DCCE] overflow-hidden p-8">
                        <x-product-photo :product="$product" credit />

                        <!-- Stock Status Badge Overlay -->
                        <div class="absolute top-4 right-4">
                            @if($product->stock > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white text-emerald-800 border border-emerald-100 shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Tersedia ({{ $product->stock }} unit)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-stone-100 text-stone-500 border border-stone-200">
                                    <span class="w-2 h-2 rounded-full bg-stone-400"></span>
                                    Stok Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Trust Micro-Badges -->
                    <div class="grid grid-cols-2 gap-3 text-center text-xs text-[#777168] pt-2">
                        <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                            <span class="block font-semibold text-[#242321]">Kota Bengkulu</span>
                            <span class="text-[11px] font-light">Asal Titik Kirim Toko</span>
                        </div>
                        <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                            <span class="block font-semibold text-[#242321]">Garansi & Orisinalitas</span>
                            <span class="text-[11px] font-light">Spesifikasi Terverifikasi</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Product Information & Purchase Area -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Brand & Category Row -->
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#A88B5A]">
                            {{ $product->brand }}
                        </span>
                        @if($product->category)
                            <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}"
                               class="px-3 py-1 rounded-full text-[11px] uppercase tracking-wider font-medium bg-[#F7F4EC] text-[#777168] hover:text-[#242321] border border-[#E5DCCE] transition-colors">
                                {{ $product->category->name }}
                            </a>
                        @endif
                    </div>

                    <!-- Product Name -->
                    <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#242321] leading-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Price & SKU -->
                    <div class="flex flex-wrap items-baseline gap-4 pb-4 border-b border-[#E5DCCE]/70">
                        <div>
                            <span class="block text-[10px] uppercase tracking-widest text-[#777168] font-medium">Harga Simulasi Demonstrasi</span>
                            <div class="font-sans text-3xl sm:text-4xl font-bold text-[#242321] mt-0.5">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="text-xs font-mono text-[#777168] bg-[#F7F4EC] px-3 py-1.5 rounded-lg border border-[#E5DCCE] self-end">
                            SKU: {{ $product->sku }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <h2 class="text-xs uppercase tracking-wider font-semibold text-[#242321]">Deskripsi & Filosofi Jam Tangan</h2>
                        <p class="text-sm text-[#777168] leading-relaxed font-light">
                            {{ $product->description }}
                        </p>
                    </div>

                    <!-- Physical Specifications Table -->
                    <div class="space-y-3 pt-2">
                        <h2 class="text-xs uppercase tracking-wider font-semibold text-[#242321]">Spesifikasi Fisik & Teknis Terverifikasi</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                                <span class="block text-[10px] uppercase tracking-wider text-[#777168] font-medium">Tipe Jam</span>
                                <span class="font-semibold text-[#242321] mt-0.5 block">{{ $product->watch_type ?? 'Analog' }}</span>
                            </div>
                            <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                                <span class="block text-[10px] uppercase tracking-wider text-[#777168] font-medium">Diameter Case</span>
                                <span class="font-semibold text-[#242321] mt-0.5 block">{{ $product->case_diameter ?? 'N/A' }}</span>
                            </div>
                            <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                                <span class="block text-[10px] uppercase tracking-wider text-[#777168] font-medium">Material Strap</span>
                                <span class="font-semibold text-[#242321] mt-0.5 block">{{ $product->strap_material ?? 'N/A' }}</span>
                            </div>
                            <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                                <span class="block text-[10px] uppercase tracking-wider text-[#777168] font-medium">Tipe Mesin (Movement)</span>
                                <span class="font-semibold text-[#242321] mt-0.5 block">{{ $product->movement_type ?? 'N/A' }}</span>
                            </div>
                            <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                                <span class="block text-[10px] uppercase tracking-wider text-[#777168] font-medium">Ketahanan Air</span>
                                <span class="font-semibold text-[#242321] mt-0.5 block">{{ $product->water_resistance ?? 'N/A' }}</span>
                            </div>
                            <div class="p-3 bg-[#F7F4EC]/60 rounded-xl border border-[#E5DCCE]/70">
                                <span class="block text-[10px] uppercase tracking-wider text-[#777168] font-medium">Warna Dial / Bezel</span>
                                <span class="font-semibold text-[#242321] mt-0.5 block">{{ $product->color ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ─── Purchase Action Area (Rule 6 Compliant) ────────── -->
                    <div class="pt-6 border-t border-[#E5DCCE] space-y-4">
                        <!-- Action Buttons and Informational Callouts -->
                        @if($product->stock <= 0)
                            <div class="p-4 bg-stone-100 rounded-2xl border border-stone-200 text-center">
                                <p class="text-sm font-medium text-stone-700">Produk ini saat ini sedang habis terjual.</p>
                                <p class="text-xs text-stone-500 mt-1">Nantikan pembaruan alokasi stok berikutnya.</p>
                            </div>
                        @else
                            @guest
                                <div class="space-y-3">
                                    <a href="{{ route('login') }}"
                                       class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-full bg-[#242321] hover:bg-[#A88B5A] text-white text-xs uppercase tracking-widest font-semibold transition-all shadow-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Masuk Akun untuk Membeli</span>
                                    </a>
                                    <p class="text-center text-xs text-[#777168] font-light">
                                        Belum memiliki akun pelanggan?
                                        <a href="{{ route('register') }}" class="font-semibold text-[#A88B5A] hover:underline">Daftar sekarang</a>
                                    </p>
                                </div>
                            @else
                                @if(auth()->user()->isAdmin())
                                    <div class="p-4 bg-stone-50 rounded-2xl border border-[#E5DCCE] flex items-center justify-between gap-4">
                                        <div class="text-xs text-[#777168]">
                                            <strong class="text-[#242321]">Mode Administrator:</strong> Anda sedang meninjau katalog produk dari sudut pandang publik.
                                        </div>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="px-4 py-2 rounded-xl bg-[#242321] hover:bg-[#A88B5A] text-white text-xs font-semibold tracking-wide transition-all shrink-0">
                                            Edit di Admin Panel
                                        </a>
                                    </div>
                                @else
                                    <form method="post" action="{{ route('customer.cart.store') }}" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <label class="text-sm">Jumlah
                                            <input name="quantity" type="number" min="1" max="{{ $product->stock }}" value="{{ old('quantity', 1) }}" required class="block w-24 rounded-lg border border-[#E5DCCE] p-2">
                                        </label>
                                        <button class="rounded-full bg-[#242321] px-6 py-3 text-sm font-semibold text-white">Tambah ke Keranjang Belanja</button>
                                    </form>
                                @endif
                            @endguest
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Related Products in Same Category ─────────────────────────── -->
        @if($relatedProducts->isNotEmpty())
            <div class="mt-14">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-widest text-[#A88B5A]">Koleksi Sejenis</span>
                        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#242321] mt-1">
                            Jam Tangan Terkait Lainnya
                        </h2>
                    </div>
                    @if($product->category)
                        <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}"
                           class="text-xs font-semibold uppercase tracking-wider text-[#777168] hover:text-[#A88B5A] transition-colors">
                            Lihat Semua {{ $product->category->name }} &rarr;
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
