@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_number)
@section('header_title', 'Detail Pesanan: ' . $order->order_number)

@section('content')
    @if ($order->payments->isNotEmpty())
        <div class="mb-4 rounded-xl border border-[#E5DCCE] bg-white p-4 text-sm">
            @foreach ($order->payments as $payment)
                <p>{{ $payment->midtrans_order_id }} · {{ $payment->payment_type ?? 'Metode belum diketahui' }} · {{ strtoupper($payment->internal_status) }}</p>
            @endforeach
            @if ($order->order_status === 'payment_review')
                <p class="font-semibold text-amber-800">Transaksi perlu ditinjau melalui status Midtrans dan catatan notifikasi.</p>
            @endif
        </div>
    @endif
    <div class="space-y-8 max-w-5xl">
        <!-- Tombol Kembali -->
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold uppercase tracking-wider text-[#777168] hover:text-[#242321] transition-colors inline-flex items-center gap-1.5">
                &larr; Kembali ke Daftar Pesanan
            </a>
        </div>

        <!-- Overview Bar -->
        <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="text-xs text-[#777168]">Tanggal Transaksi</div>
                <div class="text-sm font-semibold text-[#242321]">{{ $order->created_at->format('d F Y, H:i:s') }} WIB</div>
            </div>
            <div>
                <div class="text-xs text-[#777168]">Status Pembayaran</div>
                <div class="mt-0.5">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }} {{ $order->payment_status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }} {{ in_array($order->payment_status, ['failed','expired','canceled']) ? 'bg-rose-50 text-rose-700' : '' }}">
                        {{ strtoupper($order->payment_status) }}
                    </span>
                </div>
            </div>
            <div>
                <div class="text-xs text-[#777168]">Status Pemrosesan</div>
                <div class="mt-0.5">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#F8F6F0] text-[#242321] border border-[#E6E1D7]">
                        {{ str_replace('_', ' ', strtoupper($order->order_status)) }}
                    </span>
                </div>
            </div>
            <div>
                <div class="text-xs text-[#777168]">Status Reservasi Stok</div>
                <div class="mt-0.5 text-xs font-mono font-medium text-[#777168]">
                    {{ strtoupper($order->stock_reservation_status) }}
                </div>
            </div>
        </div>

        <!-- Two‑column grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left column: Product snapshot -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-[#E6E1D7] overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-[#E6E1D7]">
                        <h3 class="font-serif text-lg font-bold text-[#242321]">Daftar Item Jam Tangan</h3>
                        <p class="text-xs text-[#777168]">Harga dan informasi produk di bawah disimpan sebagai snapshot transaksi permanen.</p>
                    </div>
                    <div class="divide-y divide-[#E6E1D7]/60">
                        @foreach($order->items as $item)
                            <div class="p-6 flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-semibold text-sm text-[#242321]">{{ $item->product_name }}</div>
                                    <div class="text-xs text-[#777168] font-mono mt-0.5">SKU: {{ $item->product_sku }}</div>
                                    <div class="text-xs text-[#777168] mt-1">{{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}</div>
                                </div>
                                <div class="text-sm font-bold text-[#242321]">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-6 bg-[#F8F6F0]/50 border-t border-[#E6E1D7] space-y-2 text-xs">
                        <div class="flex justify-between text-[#777168]"><span>Subtotal Produk</span><span class="font-medium text-[#242321]">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-[#777168]"><span>Ongkos Kirim Flat (ke {{ $order->city_name }})</span><span class="font-medium text-[#242321]">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-sm font-bold text-[#242321] pt-2 border-t border-[#E6E1D7]"><span>Total Pembayaran</span><span class="text-[#C2A36B] font-serif text-base">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                    </div>
                </div>
            </div>
            <!-- Right column: Shipping & Account -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm space-y-4">
                    <h3 class="font-serif text-lg font-bold text-[#242321]">Alamat Penerima</h3>
                    <div class="text-xs space-y-2 text-[#777168] leading-relaxed">
                        <div>
                            <span class="block font-semibold text-[#242321] text-sm">{{ $order->recipient_name }}</span>
                            <span>{{ $order->recipient_phone }}</span>
                        </div>
                        <div class="pt-2 border-t border-[#E6E1D7]/60">
                            <p class="text-[#242321]">{{ $order->shipping_address }}</p>
                            <p>Kecamatan: {{ $order->district_name }}</p>
                            <p>{{ $order->city_name }}, {{ $order->province_name }} {{ $order->postal_code }}</p>
                        </div>
                        @if($order->notes)
                            <div class="pt-2 border-t border-[#E6E1D7]/60">
                                <span class="font-semibold text-[#242321]">Catatan Pembeli:</span>
                                <p class="italic text-[#777168] mt-0.5">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm space-y-2 text-xs">
                    <h4 class="font-semibold text-[#242321]">Akun Pemesan</h4>
                    <p class="text-[#777168]">{{ $order->user->name }} ({{ $order->user->email }})</p>
                    <p class="text-[11px] text-[#777168] pt-2 border-t border-[#E6E1D7]/60">Checkout Key: <code class="font-mono text-[#242321]">{{ $order->checkout_key }}</code></p>
                </div>
            </div>
        </div>

        <!-- Pengelolaan Pesanan -->
        @if($order->payment_status === 'paid' && $order->order_status === 'processing')
            <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm mt-8">
                <h3 class="font-serif text-lg font-bold text-[#242321] mb-4">Pengelolaan Pesanan</h3>
                <form method="POST" action="{{ route('admin.orders.status.update', $order) }}">
                    @csrf
                    <input type="hidden" name="action" value="process">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-[#777168] mb-1">Kurir Pengiriman</label>
                        <select name="courier_name" class="w-full border rounded p-2" required>
                            <option value="">-- Pilih Kurir --</option>
                            <option value="JNE">JNE</option>
                            <option value="J&T">J&T</option>
                            <option value="SiCepat">SiCepat</option>
                            <option value="AnterAja">AnterAja</option>
                            <option value="Pos Indonesia">Pos Indonesia</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[#A88B5A] text-white rounded hover:bg-[#8c7446]">Proses Pengiriman</button>
                </form>
            </div>
        @elseif($order->order_status === 'shipped')
            <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm mt-8">
                <h3 class="font-serif text-lg font-bold text-[#242321] mb-4">Informasi Pengiriman</h3>
                <p class="mb-2"><strong>Kurir:</strong> {{ $order->courier_name }}</p>
                <p class="mb-4"><strong>Nomor Resi:</strong> {{ $order->tracking_number }}</p>
                <form method="POST" action="{{ route('admin.orders.status.update', $order) }}">
                    @csrf
                    <input type="hidden" name="action" value="complete">
                   <button type="submit" class="inline-block px-4 py-2 bg-[#A88B5A] text-white rounded hover:bg-[#8C7446] cursor-pointer" style="background-color: #A88B5A; color: #FFFFFF; padding: 10px 16px; border-radius: 4px;">Selesaikan Pesanan</button>
                </form>
            </div>
        @elseif($order->order_status === 'completed')
            <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm mt-8">
                <h3 class="font-serif text-lg font-bold text-[#242321] mb-4">Pengiriman Selesai</h3>
                <p class="mb-2"><strong>Kurir:</strong> {{ $order->courier_name }}</p>
                <p class="mb-4"><strong>Nomor Resi:</strong> {{ $order->tracking_number }}</p>
                <p class="text-green-700 font-semibold">Status: Pesanan selesai</p>
            </div>
        @endif
    </div>
@endsection