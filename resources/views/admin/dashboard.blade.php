@extends('layouts.admin')

@section('title', 'Dashboard Operasional')
@section('header_title', 'Ringkasan Toko & Operasional')

@section('content')
<div class="space-y-8">
    <!-- 6 Kartu Statistik Operasional (PRD Bab 7.1) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- 1. Total Produk -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider font-semibold text-[#777168]">Total Produk</span>
                <span class="p-2.5 rounded-xl bg-[#F8F6F0] text-[#C2A36B]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold font-serif text-[#242321]">{{ $stats['total_products'] }}</span>
                <span class="text-xs text-[#777168]">({{ $stats['active_products'] }} aktif)</span>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E6E1D7]/60">
                <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                    Kelola Katalog Produk &rarr;
                </a>
            </div>
        </div>

        <!-- 2. Total Kategori -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider font-semibold text-[#777168]">Total Kategori</span>
                <span class="p-2.5 rounded-xl bg-[#F8F6F0] text-[#C2A36B]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold font-serif text-[#242321]">{{ $stats['total_categories'] }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E6E1D7]/60">
                <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                    Kelola Kategori Jam &rarr;
                </a>
            </div>
        </div>

        <!-- 3. Total Pelanggan -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider font-semibold text-[#777168]">Total Pelanggan</span>
                <span class="p-2.5 rounded-xl bg-[#F8F6F0] text-[#C2A36B]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold font-serif text-[#242321]">{{ $stats['total_customers'] }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E6E1D7]/60">
                <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                    Lihat Data Pelanggan &rarr;
                </a>
            </div>
        </div>

        <!-- 4. Total Pesanan -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider font-semibold text-[#777168]">Total Pesanan</span>
                <span class="p-2.5 rounded-xl bg-[#F8F6F0] text-[#C2A36B]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold font-serif text-[#242321]">{{ $stats['total_orders'] }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E6E1D7]/60">
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                    Daftar Semua Pesanan &rarr;
                </a>
            </div>
        </div>

        <!-- 5. Pesanan Menunggu Pembayaran -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider font-semibold text-[#777168]">Menunggu Pembayaran</span>
                <span class="p-2.5 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold font-serif text-amber-600">{{ $stats['orders_awaiting'] }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E6E1D7]/60">
                <a href="{{ route('admin.orders.index', ['order_status' => 'awaiting_payment']) }}" class="text-xs font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                    Lihat Pesanan Awaiting &rarr;
                </a>
            </div>
        </div>

        <!-- 6. Pesanan Telah Dibayar -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider font-semibold text-[#777168]">Pesanan Telah Dibayar</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold font-serif text-emerald-600">{{ $stats['orders_paid'] }}</span>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E6E1D7]/60">
                <a href="{{ route('admin.orders.index', ['payment_status' => 'paid']) }}" class="text-xs font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                    Lihat Pesanan Terverifikasi &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="bg-white p-8 rounded-2xl border border-[#E6E1D7] shadow-sm">
        <h3 class="font-serif text-xl font-bold text-[#242321]">Aksi Cepat Manajemen Toko</h3>
        <p class="mt-1 text-sm text-[#777168]">Akses cepat penambahan item katalog dan konfigurasi tarif operasional O'Clock.</p>
        <div class="mt-6 flex flex-wrap gap-4">
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white text-xs uppercase tracking-wider font-semibold transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Jam Tangan Baru
            </a>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-[#E6E1D7] hover:border-[#242321] text-[#242321] text-xs uppercase tracking-wider font-semibold transition-all bg-white hover:bg-[#F8F6F0]">
                Tambah Kategori
            </a>
            <a href="{{ route('admin.shipping-rates.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-[#E6E1D7] hover:border-[#242321] text-[#242321] text-xs uppercase tracking-wider font-semibold transition-all bg-white hover:bg-[#F8F6F0]">
                Konfigurasi Tarif Ongkir
            </a>
        </div>
    </div>
</div>
@endsection
