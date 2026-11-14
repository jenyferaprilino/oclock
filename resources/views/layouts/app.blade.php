<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F7F4EC]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "O'Clock — Timeless Style, Every Moment")</title>

    <!-- Google Fonts: Cormorant Garamond & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans text-[#242321] antialiased selection:bg-[#A88B5A]/20 selection:text-[#242321]">
    <!-- Navbar Customer / Publik (Quiet Luxury) -->
    <header class="bg-white/90 backdrop-blur-md border-b border-[#E5DCCE] sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="group flex items-center gap-2">
                <span class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-[#242321] group-hover:text-[#A88B5A] transition-colors">
                    O'Clock
                </span>
                <span class="w-1.5 h-1.5 rounded-full bg-[#A88B5A]"></span>
            </a>

            <!-- Nav Links Desktop -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="{{ route('home') }}"
                   class="transition-colors {{ request()->routeIs('home') ? 'text-[#A88B5A] font-semibold' : 'text-[#777168] hover:text-[#242321]' }}">
                    Beranda
                </a>
                <a href="{{ route('catalog.index') }}"
                   class="transition-colors {{ request()->routeIs('catalog.*') ? 'text-[#A88B5A] font-semibold' : 'text-[#777168] hover:text-[#242321]' }}">
                    Koleksi Jam Tangan
                </a>
            </nav>

            <!-- Search Bar Mini Desktop -->
            <div class="hidden lg:block w-64">
                <form method="GET" action="{{ route('catalog.index') }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari brand, model, SKU..."
                               class="w-full pl-9 pr-3 py-2 bg-[#F7F4EC]/60 border border-[#E5DCCE] rounded-full text-xs text-[#242321] placeholder-[#777168]/70 focus:outline-none focus:ring-1 focus:ring-[#A88B5A] focus:border-[#A88B5A] transition-all">
                        <svg class="w-3.5 h-3.5 text-[#777168] absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </form>
            </div>

            <!-- User Auth Navigation -->
            <div class="flex items-center gap-4 text-sm font-medium">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="text-xs font-semibold px-3.5 py-2 rounded-xl bg-[#242321] text-white hover:bg-[#A88B5A] transition-all shadow-xs">
                            Admin Panel
                        </a>
                    @else
                        <a href="{{ route('customer.cart.index') }}" class="rounded-full border border-[#E5DCCE] px-3 py-1.5 text-xs font-semibold hover:border-[#A88B5A] transition-colors {{ request()->routeIs('customer.cart.*') ? 'border-[#A88B5A] text-[#A88B5A] bg-[#A88B5A]/5' : '' }}">
                            Keranjang ({{ auth()->user()->cart?->items()->sum('quantity') ?? 0 }})
                        </a>
                        <a href="{{ route('customer.orders.index') }}" class="rounded-full border border-[#E5DCCE] px-3 py-1.5 text-xs font-semibold hover:border-[#A88B5A] transition-colors {{ request()->routeIs('customer.orders.*') || request()->routeIs('customer.payment.*') ? 'border-[#A88B5A] text-[#A88B5A] bg-[#A88B5A]/5' : '' }}">
                            Pesanan Saya
                        </a>
                        <a href="{{ route('customer.profile') }}"
                           class="flex items-center gap-2 text-xs font-semibold px-3.5 py-1.5 rounded-full border border-[#E5DCCE] text-[#242321] hover:border-[#A88B5A] hover:text-[#A88B5A] transition-all {{ request()->routeIs('customer.profile') ? 'border-[#A88B5A] text-[#A88B5A] bg-[#A88B5A]/5' : '' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ Str::words(auth()->user()->name, 1, '') }}</span>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs uppercase tracking-wider px-3 py-1.5 text-[#777168] hover:text-[#242321] transition-colors cursor-pointer">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs uppercase tracking-wider font-semibold text-[#777168] hover:text-[#242321] transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 rounded-full bg-[#A88B5A] hover:bg-[#957849] text-white text-xs tracking-wider uppercase font-semibold transition-all shadow-xs">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>

        <!-- Mobile Navigation Bar Strip -->
        <div class="md:hidden border-t border-[#E5DCCE] px-4 py-2 flex items-center justify-around text-xs font-medium text-[#777168] bg-[#F7F4EC]/90">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#A88B5A] font-bold' : '' }}">Beranda</a>
            <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'text-[#A88B5A] font-bold' : '' }}">Koleksi Jam</a>
            @auth
                @if(auth()->user()->isCustomer())
                    <a href="{{ route('customer.orders.index') }}" class="{{ request()->routeIs('customer.orders.*') || request()->routeIs('customer.payment.*') ? 'text-[#A88B5A] font-bold' : '' }}">Pesanan</a>
                    <a href="{{ route('customer.profile') }}" class="{{ request()->routeIs('customer.profile') ? 'text-[#A88B5A] font-bold' : '' }}">Profil Akun</a>
                @endif
            @endauth
        </div>
    </header>

    <!-- Flash Alert Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if ($errors->any())
            <ul role="alert" class="mb-4 list-disc rounded-xl border border-rose-200 bg-rose-50 py-3 pl-8 pr-4 text-sm text-rose-800">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
        @if(session('success'))
            <div class="bg-emerald-50/80 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-xs">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif
        @if(session('info'))
            <div class="bg-stone-50 border border-[#E5DCCE] text-[#242321] px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-xs">
                <span>{{ session('info') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-stone-400 hover:text-stone-700">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer O'Clock Refined Luxury -->
    <footer class="border-t border-[#E5DCCE] bg-white pt-14 pb-8 text-[#777168] text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-[#E5DCCE]/60">
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="font-serif text-2xl font-bold text-[#242321]">O'Clock</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#A88B5A]"></span>
                    </div>
                    <p class="font-serif italic text-sm text-[#A88B5A]">Timeless Style, Every Moment</p>
                    <p class="text-xs text-[#777168] max-w-sm leading-relaxed font-light">
                        Butik jam tangan kurasi terpercaya yang menghadirkan perpaduan estetika refined luxury, presisi horologi internasional, dan mahakarya pengrajin jam tangan Indonesia.
                    </p>
                    <div class="pt-2 text-[11px] text-[#777168]">
                        Pusat Distribusi & Pengiriman: <strong class="text-[#242321]">Kota Bengkulu, Indonesia</strong>
                    </div>
                </div>

                <!-- Col 2: Navigasi Toko -->
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-[#242321]">Koleksi & Butik</h4>
                    <ul class="space-y-1.5 font-light">
                        <li><a href="{{ route('home') }}" class="hover:text-[#242321] transition-colors">Beranda Utama</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-[#242321] transition-colors">Semua Jam Tangan</a></li>
                        <li><a href="{{ route('catalog.index', ['sort' => 'price_desc']) }}" class="hover:text-[#242321] transition-colors">Koleksi Eksklusif</a></li>
                        <li><a href="{{ route('catalog.index', ['search' => 'Alexandre Christie']) }}" class="hover:text-[#242321] transition-colors">Alexandre Christie</a></li>
                    </ul>
                </div>

                <!-- Col 3: Akun & Informasi -->
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-[#242321]">Akun Belanja</h4>
                    <ul class="space-y-1.5 font-light">
                        @auth
                            @if(auth()->user()->isCustomer())
                                <li><a href="{{ route('customer.profile') }}" class="hover:text-[#242321] transition-colors">Profil Saya</a></li>
                                <li><a href="{{ route('customer.orders.index') }}" class="hover:text-[#242321] transition-colors">Riwayat Pesanan</a></li>
                            @else
                                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-[#242321] transition-colors">Dashboard Admin</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-[#242321] transition-colors">Masuk Akun</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-[#242321] transition-colors">Daftar Pelanggan</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <!-- Bottom Disclaimer -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-[#777168] font-light">
                <p>&copy; {{ date('Y') }} O'Clock Watch E-Commerce. Data katalog demonstrasi terkurasi.</p>
                <p>Designed with Quiet Luxury & Refined Minimalism aesthetic.</p>
            </div>
        </div>
    </footer>
</body>
</html>
