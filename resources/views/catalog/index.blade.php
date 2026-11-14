@extends('layouts.app')

@section('title', "Koleksi Jam Tangan — O'Clock")

@section('content')
<div class="bg-[#F7F4EC] min-h-screen py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ─── Header & Breadcrumbs ──────────────────────────────────────── -->
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs text-[#777168] mb-3 font-light">
                <a href="{{ route('home') }}" class="hover:text-[#242321] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-[#242321] font-medium">Koleksi Jam Tangan</span>
                @if($selectedCategory)
                    <span>/</span>
                    <span class="text-[#A88B5A] font-semibold">{{ $selectedCategory->name }}</span>
                @endif
            </nav>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#242321]">
                        @if($selectedCategory)
                            Koleksi {{ $selectedCategory->name }}
                        @else
                            Koleksi Jam Tangan
                        @endif
                    </h1>
                    <p class="mt-2 text-sm text-[#777168] max-w-2xl font-light">
                        Kurasi jam tangan internasional dan lokal Indonesia dengan presisi tinggi, estetika Quiet Luxury, dan spesifikasi terverifikasi.
                    </p>
                </div>

                <div class="text-xs text-[#777168] font-light">
                    Menampilkan <strong class="text-[#242321] font-semibold">{{ $products->total() }}</strong> jam tangan aktif
                </div>
            </div>
        </div>

        <!-- ─── Filter & Search Control Bar ───────────────────────────────── -->
        <div class="bg-white rounded-2xl border border-[#E5DCCE] p-4 sm:p-6 mb-8 shadow-xs">
            <form method="GET" action="{{ route('catalog.index') }}" class="space-y-4" id="filterForm">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    <!-- Search Input (Name, Brand, SKU) -->
                    <div class="md:col-span-8 relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari berdasarkan nama model, brand (Alexandre Christie, Seiko, Casio, EIGER, PALA, Woodka), atau SKU..."
                               class="w-full pl-10 pr-4 py-2.5 bg-[#F7F4EC]/50 border border-[#E5DCCE] rounded-xl text-sm text-[#242321] placeholder-[#777168]/70 focus:outline-none focus:ring-1 focus:ring-[#A88B5A] focus:border-[#A88B5A] transition-all">
                        <svg class="w-4 h-4 text-[#777168] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="md:col-span-4 flex items-center gap-2">
                        <label for="sortSelect" class="text-xs uppercase tracking-wider font-semibold text-[#777168] shrink-0">Urutkan:</label>
                        <select id="sortSelect"
                                name="sort"
                                onchange="this.form.submit()"
                                class="w-full py-2.5 px-3 bg-[#F7F4EC]/50 border border-[#E5DCCE] rounded-xl text-xs font-medium text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#A88B5A] focus:border-[#A88B5A] cursor-pointer">
                            <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Terbaru Dirilis</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Terendah ke Tertinggi</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tertinggi ke Terendah</option>
                        </select>
                    </div>
                </div>

                <!-- Category Filter Pills -->
                <div class="pt-3 border-t border-[#E5DCCE]/60 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                    <span class="text-xs uppercase tracking-wider font-semibold text-[#777168] shrink-0 mr-1">Kategori:</span>

                    <!-- All Categories Pill -->
                    <a href="{{ route('catalog.index', array_filter(['search' => request('search'), 'sort' => request('sort')])) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all shrink-0 {{ !request('category') ? 'bg-[#242321] text-white shadow-2xs' : 'bg-[#F7F4EC] text-[#777168] hover:text-[#242321] border border-[#E5DCCE]' }}">
                        Semua Kategori
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('catalog.index', array_filter(['category' => $cat->slug, 'search' => request('search'), 'sort' => request('sort')])) }}"
                           class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all shrink-0 {{ request('category') === $cat->slug ? 'bg-[#A88B5A] text-white shadow-2xs' : 'bg-[#F7F4EC] text-[#777168] hover:text-[#242321] border border-[#E5DCCE]' }}">
                            {{ $cat->name }}
                            <span class="ml-1 opacity-75 text-[10px]">({{ $cat->products_count }})</span>
                        </a>
                    @endforeach
                </div>
            </form>

            <!-- Active Filters Summary & Reset -->
            @if(request()->hasAny(['search', 'category', 'sort']))
                <div class="mt-4 pt-3 border-t border-[#E5DCCE]/60 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex flex-wrap items-center gap-2 text-[#777168]">
                        <span>Filter Aktif:</span>
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#F7F4EC] text-[#242321] border border-[#E5DCCE]">
                                Pencarian: "<strong>{{ request('search') }}</strong>"
                            </span>
                        @endif
                        @if($selectedCategory)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#F7F4EC] text-[#242321] border border-[#E5DCCE]">
                                Kategori: <strong>{{ $selectedCategory->name }}</strong>
                            </span>
                        @endif
                        @if(request('sort'))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#F7F4EC] text-[#242321] border border-[#E5DCCE]">
                                Urutan: <strong>{{ request('sort') === 'price_asc' ? 'Harga Terendah' : 'Harga Tertinggi' }}</strong>
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('catalog.index') }}" class="font-medium text-[#A88B5A] hover:text-[#957849] underline transition-colors">
                        Reset Semua Filter
                    </a>
                </div>
            @endif
        </div>

        <!-- ─── Products Grid ─────────────────────────────────────────────── -->
        @if($products->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <!-- ─── Pagination Links ──────────────────────────────────────── -->
            <div class="mt-12 flex justify-center">
                {{ $products->links() }}
            </div>
        @else
            <!-- ─── Empty State ───────────────────────────────────────────── -->
            <div class="bg-white rounded-3xl border border-[#E5DCCE] p-12 sm:p-16 text-center max-w-xl mx-auto shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-[#F7F4EC] border border-[#E5DCCE] flex items-center justify-center text-[#777168]/60 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-2xl font-bold text-[#242321]">Jam Tangan Tidak Ditemukan</h3>
                <p class="text-sm text-[#777168] mt-2 max-w-sm mx-auto font-light">
                    Tidak ada produk jam tangan yang cocok dengan kriteria pencarian atau filter yang Anda pilih.
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <a href="{{ route('catalog.index') }}"
                       class="px-6 py-2.5 rounded-full bg-[#242321] hover:bg-[#A88B5A] text-white text-xs uppercase tracking-wider font-semibold transition-all shadow-xs">
                        Tampilkan Semua Jam Tangan
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
