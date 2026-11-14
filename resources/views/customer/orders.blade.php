@extends('layouts.app')

@section('title', "Riwayat Pesanan — O'Clock")

@section('content')
<div class="bg-[#F7F4EC] min-h-screen py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ─── Breadcrumb Navigation ─────────────────────────────────────── -->
        <nav class="flex items-center gap-2 text-xs text-[#777168] mb-6 font-light">
            <a href="{{ route('home') }}" class="hover:text-[#242321] transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-[#242321] font-medium">Riwayat Pesanan</span>
        </nav>

        <!-- ─── Header Section ────────────────────────────────────────────── -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#242321]">Riwayat Pesanan</h1>
                <p class="text-xs text-[#777168] mt-1 font-light">Daftar transaksi pembelian jam tangan dan status verifikasi pembayaran Anda.</p>
            </div>
            <a href="{{ route('catalog.index') }}"
               class="self-start sm:self-auto inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#E5DCCE] hover:border-[#A88B5A] bg-white text-xs font-semibold text-[#242321] hover:text-[#A88B5A] transition-all shadow-2xs">
                <span>Belanja Lagi</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        @if(session('info'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- ─── Order List ────────────────────────────────────────────────── -->
        <div class="space-y-4">
            @forelse ($orders as $order)
                <article class="bg-white rounded-3xl border border-[#E5DCCE] p-6 shadow-xs hover:border-[#A88B5A]/40 transition-all">
                    <div class="flex flex-wrap items-start justify-between gap-4 pb-4 border-b border-[#E5DCCE]/60">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-sm font-bold text-[#242321]">{{ $order->order_number }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                    {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                    {{ $order->payment_status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ in_array($order->payment_status, ['failed', 'expired', 'canceled']) ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                    {{ $order->payment_status === 'review' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}">
                                    {{ match ($order->payment_status) {
                                        'paid' => 'Pembayaran terverifikasi',
                                        'failed' => 'Pembayaran gagal',
                                        'expired' => 'Pembayaran kedaluwarsa',
                                        'canceled' => 'Pembayaran dibatalkan',
                                        'refunded' => 'Pembayaran dikembalikan',
                                        'review' => 'Pembayaran perlu diperiksa',
                                        default => 'Menunggu pembayaran',
                                    } }}
                                </span>
                            </div>
                            <p class="text-xs text-[#777168] mt-1 font-light">
                                {{ $order->created_at->format('d/m/Y H:i') }} WIB · Penerima: {{ $order->recipient_name }} ({{ $order->city_name }})
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="text-xs text-[#777168] block">Total Pembayaran</span>
                            <span class="font-serif text-lg font-bold text-[#242321]">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    @if($order->items->isNotEmpty())
                        <div class="py-3.5 space-y-2 border-b border-[#E5DCCE]/40 text-xs text-[#777168]">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-[#242321]">{{ $item->product_name }}</span>
                                        <span class="text-[10px] text-[#777168] font-mono">({{ $item->product_sku }})</span>
                                        <span>× {{ $item->quantity }}</span>
                                    </div>
                                    <span class="font-mono">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            @if($order->shipping_cost > 0)
                                <div class="flex items-center justify-between text-[11px] text-[#777168]/80 pt-1">
                                    <span>Ongkos kirim ({{ $order->city_name }})</span>
                                    <span class="font-mono">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="pt-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="text-[11px] text-[#777168] font-light">
                            Status Pesanan: <span class="font-semibold text-[#242321] uppercase">{{ str_replace('_', ' ', $order->order_status) }}</span>
                        </div>
                        @if(in_array($order->order_status, ['shipped','completed']))
                            <div class="mt-1 text-[11px] text-[#777168]">
                                <span class="font-medium">Kurir:</span> {{ $order->courier_name ?? '-' }}<br>
                                <span class="font-medium">No. Resi:</span> {{ $order->tracking_number ?? '-' }}
                            </div>
                        @endif

                        @if ($order->payments_exists)
                            <a href="{{ route('customer.payment.show', $order) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-[#A88B5A] text-[#A88B5A] hover:bg-[#A88B5A] hover:text-white text-xs font-semibold tracking-wider transition-all shadow-2xs">
                                <span>Detail pembayaran</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                        @if($order->payment_status === 'paid')
                            <a href="{{ route('customer.orders.invoice', $order) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-[#6FAE4A] text-[#6FAE4A] hover:bg-[#6FAE4A] hover:text-white text-xs font-semibold tracking-wider transition-all shadow-2xs ml-2">
                                <span>Cetak Invoice</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/></svg>
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="bg-white rounded-3xl border border-[#E5DCCE] p-12 text-center shadow-xs">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#F7F4EC] border border-[#E5DCCE] flex items-center justify-center text-[#A88B5A]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <h2 class="font-serif text-xl font-bold text-[#242321] mb-1">Belum Ada Pesanan</h2>
                    <p class="text-xs text-[#777168] max-w-sm mx-auto mb-6 font-light">Anda belum memiliki riwayat pesanan jam tangan di O'Clock. Jelajahi koleksi butik kami dan temukan jam tangan impian Anda.</p>
                    <a href="{{ route('catalog.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#242321] hover:bg-[#A88B5A] text-white text-xs font-semibold uppercase tracking-wider transition-all shadow-xs">
                        <span>Jelajahi Koleksi Jam</span>
                    </a>
                </div>
            @endforelse
        </div>

        @if($orders->hasPages())
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
