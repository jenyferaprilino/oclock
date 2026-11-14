@extends('layouts.admin')

@section('title', 'Daftar Pesanan')
@section('header_title', 'Semua Pesanan Pelanggan')

@section('content')
<div class="space-y-6">
    <!-- Notice Aturan BR-11 & BR-16 -->
    <div class="bg-white p-5 rounded-2xl border border-[#E6E1D7] flex items-center gap-4 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-[#242321] text-[#F8F6F0] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs text-[#777168] leading-relaxed">
            <span class="font-semibold text-[#242321]">Prinsip Integritas Transaksi (PRD Bab 7.5 & Aturan BR-11/BR-16):</span>
            Admin tidak diperbolehkan menetapkan status pembayaran menjadi <em>paid</em> secara manual. Status bayar hanya diubah melalui verifikasi webhook Midtrans (Tahap 6). Pemrosesan & pengiriman operasional diaktifkan pada Tahap 7.
        </div>
    </div>

    <!-- Filter Pesanan -->
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="No order, nama penerima, email..."
               class="px-4 py-2 bg-white border border-[#E6E1D7] rounded-xl text-xs text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#C2A36B] w-64">

        <select name="payment_status" class="px-3 py-2 bg-white border border-[#E6E1D7] rounded-xl text-xs text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#C2A36B]">
            <option value="">Semua Status Bayar</option>
            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="expired" {{ request('payment_status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>

        <select name="order_status" class="px-3 py-2 bg-white border border-[#E6E1D7] rounded-xl text-xs text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#C2A36B]">
            <option value="">Semua Status Order</option>
            <option value="awaiting_payment" {{ request('order_status') === 'awaiting_payment' ? 'selected' : '' }}>Awaiting Payment</option>
            <option value="processing" {{ request('order_status') === 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="packed" {{ request('order_status') === 'packed' ? 'selected' : '' }}>Packed</option>
            <option value="shipped" {{ request('order_status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="completed" {{ request('order_status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="canceled" {{ request('order_status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-[#242321] text-white text-xs font-semibold uppercase tracking-wider rounded-xl hover:bg-[#C2A36B] transition-all cursor-pointer">
            Filter
        </button>
        @if(request()->hasAny(['search', 'payment_status', 'order_status']))
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#777168] hover:text-[#242321] underline">Reset</a>
        @endif
    </form>

    <!-- Table Pesanan -->
    <div class="bg-white rounded-2xl border border-[#E6E1D7] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E6E1D7] text-left text-sm">
                <thead class="bg-[#F8F6F0]/60 text-xs uppercase tracking-wider text-[#777168]">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nomor Order</th>
                        <th class="px-6 py-4 font-semibold">Pelanggan</th>
                        <th class="px-6 py-4 font-semibold">Total Belanja</th>
                        <th class="px-6 py-4 font-semibold">Status Bayar</th>
                        <th class="px-6 py-4 font-semibold">Status Operasional</th>
                        <th class="px-6 py-4 font-semibold text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E1D7]/60">
                    @forelse($orders as $order)
                        <tr class="hover:bg-[#F8F6F0]/30 transition-colors">
                            <td class="px-6 py-4 font-mono font-medium text-xs text-[#242321]">
                                {{ $order->order_number }}
                                <div class="text-[10px] font-sans text-[#777168]">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-[#242321]">{{ $order->recipient_name }}</div>
                                <div class="text-xs text-[#777168]">{{ $order->city_name }}, {{ $order->province_name }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-[#242321]">
                                Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $order->payment_status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ in_array($order->payment_status, ['failed', 'expired', 'canceled']) ? 'bg-rose-50 text-rose-700' : '' }}">
                                    {{ strtoupper($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-[#F8F6F0] text-[#242321] border border-[#E6E1D7]">
                                    {{ str_replace('_', ' ', strtoupper($order->order_status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-xs font-semibold text-[#C2A36B] hover:text-[#AF8F55] transition-colors">
                                    Lihat Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[#777168]">
                                Belum ada transaksi pesanan yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-[#E6E1D7]">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
