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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans text-[#242321] antialiased selection:bg-[#A88B5A]/20 selection:text-[#242321]">
    <!-- Navbar Minimalis -->
    <header class="bg-white/80 backdrop-blur-md border-b border-[#E5DCCE] sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="group flex items-center gap-2">
                <span class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-[#242321] group-hover:text-[#A88B5A] transition-colors">
                    O'Clock
                </span>
                <span class="w-1.5 h-1.5 rounded-full bg-[#A88B5A]"></span>
            </a>

            <nav class="flex items-center gap-4 sm:gap-6 text-sm font-medium">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-[#242321] hover:text-[#A88B5A] transition-colors">
                            Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('customer.profile') }}" class="text-[#242321] hover:text-[#A88B5A] transition-colors">
                            Profil Saya
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs tracking-wider uppercase px-4 py-2 rounded-full border border-[#E5DCCE] hover:border-[#242321] text-[#777168] hover:text-[#242321] transition-all">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-[#777168] hover:text-[#242321] transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-[#A88B5A] hover:bg-[#957849] text-white text-xs tracking-wider uppercase font-semibold transition-all shadow-xs">
                        Daftar
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Flash Alert Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-xs">
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

    <!-- Main Content -->
    <main class="flex-1 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Footer Minimalis -->
    <footer class="border-t border-[#E5DCCE] bg-white/50 py-8 text-center text-xs text-[#777168]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} O'Clock Watch E-Commerce. All rights reserved.</p>
            <p class="font-serif italic text-sm text-[#A88B5A]">Timeless Style, Every Moment</p>
        </div>
    </footer>
</body>
</html>
