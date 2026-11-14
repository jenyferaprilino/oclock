<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F6F0]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "O'Clock Admin Panel")</title>

    <!-- Google Fonts: Cormorant Garamond & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans text-[#242321] antialiased selection:bg-[#C2A36B]/20 selection:text-[#242321]">
    <!-- Navbar Minimalis Khusus Admin Portal -->
    <header class="bg-white border-b border-[#E6E1D7] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-[#242321]">
                    O'Clock
                </span>
                <span class="w-1.5 h-1.5 rounded-full bg-[#C2A36B]"></span>
                <span class="text-xs uppercase tracking-wider font-semibold px-2.5 py-1 rounded-md bg-[#242321] text-white">
                    Admin Portal
                </span>
            </div>

            <div class="text-xs text-[#777168]">
                <a href="{{ route('home') }}" class="hover:text-[#242321] transition-colors">
                    &larr; Kembali ke Website Publik
                </a>
            </div>
        </div>
    </header>

    <!-- Flash Alert Notifications -->
    <div class="max-w-md mx-auto px-4 w-full mt-6">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-sm">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif
        @if(session('info'))
            <div class="bg-stone-50 border border-[#E6E1D7] text-[#242321] px-4 py-3 rounded-xl text-sm flex items-center justify-between mb-4 shadow-sm">
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
    <footer class="border-t border-[#E6E1D7] bg-white/50 py-6 text-center text-xs text-[#777168]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} O'Clock Internal Management. Akses Terbatas.</p>
            <p class="font-serif italic text-sm text-[#C2A36B]">Timeless Style, Every Moment</p>
        </div>
    </footer>
</body>
</html>
