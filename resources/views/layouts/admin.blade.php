<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F7F4EC]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — O'Clock</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans text-[#242321] antialiased selection:bg-[#A88B5A]/20 selection:text-[#242321]">
    <div class="min-h-full flex">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-white border-r border-[#E5DCCE] flex flex-col shrink-0">
            <!-- Brand Logo -->
            <div class="h-20 flex items-center px-6 border-b border-[#E5DCCE] gap-2">
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-2">
                    <span class="font-serif text-2xl font-bold tracking-tight text-[#242321] group-hover:text-[#A88B5A] transition-colors">
                        O'Clock
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full bg-[#A88B5A]"></span>
                </a>
                <span class="ml-auto text-[10px] tracking-wider uppercase font-semibold px-2 py-0.5 rounded bg-[#F7F4EC] text-[#777168] border border-[#E5DCCE]">
                    Admin
                </span>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#F7F4EC] text-[#242321] font-semibold border border-[#E5DCCE]' : 'text-[#777168] hover:text-[#242321] hover:bg-[#F7F4EC]/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-[#A88B5A]' : 'text-[#777168]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-[#F7F4EC] text-[#242321] font-semibold border border-[#E5DCCE]' : 'text-[#777168] hover:text-[#242321] hover:bg-[#F7F4EC]/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.categories.*') ? 'text-[#A88B5A]' : 'text-[#777168]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Kategori
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.products.*') ? 'bg-[#F7F4EC] text-[#242321] font-semibold border border-[#E5DCCE]' : 'text-[#777168] hover:text-[#242321] hover:bg-[#F7F4EC]/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.products.*') ? 'text-[#A88B5A]' : 'text-[#777168]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Produk Jam Tangan
                </a>

                <a href="{{ route('admin.shipping-rates.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.shipping-rates.*') ? 'bg-[#F7F4EC] text-[#242321] font-semibold border border-[#E5DCCE]' : 'text-[#777168] hover:text-[#242321] hover:bg-[#F7F4EC]/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.shipping-rates.*') ? 'text-[#A88B5A]' : 'text-[#777168]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                    Tarif Pengiriman
                </a>

                <a href="{{ route('admin.customers.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.customers.*') ? 'bg-[#F7F4EC] text-[#242321] font-semibold border border-[#E5DCCE]' : 'text-[#777168] hover:text-[#242321] hover:bg-[#F7F4EC]/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.customers.*') ? 'text-[#A88B5A]' : 'text-[#777168]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Pelanggan
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-[#F7F4EC] text-[#242321] font-semibold border border-[#E5DCCE]' : 'text-[#777168] hover:text-[#242321] hover:bg-[#F7F4EC]/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.orders.*') ? 'text-[#A88B5A]' : 'text-[#777168]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Pesanan
                </a>

                <div class="pt-4 pb-1">
                    <span class="px-3 text-[10px] font-semibold uppercase tracking-wider text-[#777168]/70">Pengaturan</span>
                </div>

                <a href="{{ route('admin.register') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.register') ? 'bg-[#F7F4EC] text-[#242321] font-semibold border border-[#E5DCCE]' : 'text-[#777168] hover:text-[#242321] hover:bg-[#F7F4EC]/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.register') ? 'text-[#A88B5A]' : 'text-[#777168]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Tambah Admin
                </a>
            </nav>

            <!-- User Info & Logout -->
            <div class="p-4 border-t border-[#E5DCCE]">
                <div class="px-3 py-2 rounded-xl bg-[#F7F4EC] border border-[#E5DCCE]/70 mb-3">
                    <p class="text-xs font-semibold text-[#242321] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-[#777168] truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 rounded-xl border border-[#E5DCCE] hover:border-[#242321] text-xs font-semibold uppercase tracking-wider text-[#777168] hover:text-[#242321] transition-all cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar Header -->
            <header class="h-20 bg-white border-b border-[#E5DCCE] flex items-center justify-between px-6 lg:px-8">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-[#242321]">
                        @yield('header_title', 'Dashboard')
                    </h2>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" target="_blank" class="text-xs tracking-wider uppercase font-semibold text-[#777168] hover:text-[#A88B5A] transition-colors flex items-center gap-1.5">
                        <span>Lihat Toko</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </header>

            <!-- Alerts -->
            <div class="px-6 lg:px-8 pt-6">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-xs animate-fade-in">
                        <span>{{ session('success') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-xs animate-fade-in">
                        <span>{{ session('error') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Page Body -->
            <main class="flex-1 p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
