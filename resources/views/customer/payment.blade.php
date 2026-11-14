@extends('layouts.app')

@section('title', "Pembayaran — O'Clock")

@section('content')
<div class="bg-[#F7F4EC] min-h-screen py-10 sm:py-14">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        <!-- ─── Breadcrumb Navigation ─────────────────────────────────────── -->
        <nav class="flex items-center gap-2 text-xs text-[#777168] mb-6 font-light">
            <a href="{{ route('home') }}" class="hover:text-[#242321] transition-colors">Beranda</a>
            <span>/</span>
            <a href="{{ route('customer.orders.index') }}" class="hover:text-[#242321] transition-colors">Riwayat Pesanan</a>
            <span>/</span>
            <span class="text-[#242321] font-medium">Pembayaran Pesanan</span>
        </nav>

        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[#777168] hover:text-[#242321] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Lihat Pesanan Saya</span>
            </a>
            <span class="text-[11px] text-[#777168] font-mono">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
        </div>

        @if(session('info'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Live Status Success Toast (Dynamic via Auto-Polling) -->
        <div id="payment-success-toast" class="{{ $order->payment_status === 'paid' ? '' : 'hidden' }} mb-6 p-5 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs shadow-xs animate-fade-in">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-sm text-emerald-800">Pembayaran Berhasil!</h3>
                    <p class="text-[11px] text-emerald-700 mt-0.5 leading-relaxed">
                        Transaksi Anda telah terverifikasi resmi oleh sistem Midtrans. Pesanan kini sedang dipersiapkan dan diproses oleh tim O'Clock.
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('customer.orders.index') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-700 hover:bg-emerald-800 text-white text-[11px] font-semibold tracking-wider uppercase transition-all shadow-2xs">
                            <span>Lihat Pesanan Saya</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-[#E5DCCE] bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <!-- Header Title -->
            <div class="border-b border-[#E5DCCE]/60 pb-5">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-[#A88B5A] bg-[#A88B5A]/10 px-3 py-1 rounded-full">
                        {{ ($isProduction ?? config('midtrans.is_production')) ? 'Midtrans Payment Gateway' : 'Midtrans Sandbox Gateway' }}
                    </span>
                    <span class="font-mono text-xs text-[#777168]">ID: {{ $order->order_number }}</span>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#242321] mt-3">Pembayaran Pesanan</h1>
                <p class="text-xs text-[#777168] mt-1 font-light">Selesaikan transaksi belanja Anda melalui layanan pembayaran aman multi-metode.</p>
            </div>

            <!-- Order Snapshot Details -->
            <div class="bg-[#F7F4EC]/60 rounded-2xl p-4 border border-[#E5DCCE]/70 space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[#777168]">Nomor Pesanan</span>
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-[#242321]">Pesanan <strong>{{ $order->order_number }}</strong></span>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $order->order_number }}'); alert('Nomor pesanan disalin!');"
                                class="p-1 text-[#777168] hover:text-[#242321] transition-colors" title="Salin Nomor Pesanan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#777168]">Tujuan Pengiriman</span>
                    <span class="text-[#242321] font-medium">{{ $order->recipient_name }} · {{ $order->city_name }}</span>
                </div>
                @if($payment->payment_type)
                    <div class="flex items-center justify-between">
                        <span class="text-[#777168]">Metode Terdeteksi</span>
                        <span class="font-mono uppercase font-semibold text-[#A88B5A]">{{ str_replace('_', ' ', $payment->payment_type) }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between pt-2.5 border-t border-[#E5DCCE]/60 text-sm">
                    <span class="font-medium text-[#242321]">Total Pembayaran</span>
                    <span class="font-serif text-lg font-bold text-[#A88B5A]">Total <strong>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong></span>
                </div>
            </div>

            <!-- Status Banner -->
            <div id="payment-status-card" class="rounded-2xl p-4 border text-xs
                {{ $order->payment_status === 'paid' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : '' }}
                {{ $order->payment_status === 'pending' ? 'bg-amber-50 border-amber-200 text-amber-900' : '' }}
                {{ in_array($order->payment_status, ['failed', 'expired', 'canceled']) ? 'bg-rose-50 border-rose-200 text-rose-900' : '' }}
                {{ $order->payment_status === 'review' ? 'bg-purple-50 border-purple-200 text-purple-900' : '' }}">
                <div class="font-semibold text-sm mb-1 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        @if($order->payment_status === 'paid')
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($order->payment_status === 'pending')
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @endif
                        <span>Status Transaksi</span>
                    </div>
                    @if($order->payment_status === 'pending')
                        <span id="auto-polling-indicator" class="inline-flex items-center gap-1.5 text-[10px] text-amber-800 bg-amber-100/80 px-2.5 py-0.5 rounded-full font-normal">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Pengecekan otomatis (12 dtk)</span>
                        </span>
                    @endif
                </div>
                <p id="payment-status-text" role="status" class="leading-relaxed">{{ match ($order->payment_status) {
                    'paid' => 'Pembayaran terverifikasi.',
                    'failed' => 'Pembayaran gagal. Silakan buat pesanan baru.',
                    'expired' => 'Pembayaran kedaluwarsa. Silakan buat pesanan baru.',
                    'canceled' => 'Pembayaran dibatalkan.',
                    'review' => 'Status pembayaran perlu diperiksa. Hindari pembayaran ulang.',
                    default => 'Menunggu pembayaran terverifikasi.',
                } }}</p>
                @if($order->payment_status === 'pending' && $order->expires_at)
                    <p class="text-[11px] text-amber-800/80 mt-1">Batas waktu pembayaran: {{ $order->expires_at->format('d M Y, H:i') }} WIB ({{ $order->expires_at->diffForHumans() }})</p>
                @endif
            </div>

            <!-- Action Buttons Area -->
            <div class="space-y-3 pt-2">
                @if ($order->payment_status === 'pending' && $order->expires_at?->isFuture()
                    && str_starts_with($payment->snap_redirect_url ?? '', 'https://app.sandbox.midtrans.com/snap/'))
                    <!-- 1. Tombol Buka Midtrans -->
                    <a href="{{ $payment->snap_redirect_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[#242321] hover:bg-[#A88B5A] px-6 py-3.5 text-xs uppercase tracking-wider font-semibold text-white transition-all shadow-xs group">
                        <svg class="w-4 h-4 text-[#A88B5A] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Buka Midtrans (Buka pembayaran)</span>
                    </a>
                @endif

                <!-- 2. Tombol Pay / Simulasikan Pembayaran (Hanya Mode Sandbox & Pending) -->
                @unless($isProduction ?? config('midtrans.is_production'))
                    @if($order->payment_status === 'pending')
                        <div class="bg-[#F7F4EC] p-4 rounded-2xl border border-[#E5DCCE] space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-[#242321] flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#A88B5A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Simulator Resmi Midtrans Sandbox</span>
                                </span>
                                <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded bg-[#A88B5A]/15 text-[#A88B5A]">Buka di Tab Baru</span>
                            </div>
                            <p class="text-[11px] text-[#777168] leading-relaxed">
                                Pilih metode pembayaran yang Anda ambil di Snap, lalu klik tombol di bawah untuk membuka simulator resmi Midtrans Sandbox:
                            </p>

                            <!-- Simulator Method Quick Selector -->
                            <div class="space-y-2">
                                <label class="block text-[11px] font-semibold text-[#242321] uppercase tracking-wider">Metode Simulator:</label>
                                <select id="simulator-method-select"
                                        class="w-full rounded-xl border border-[#E5DCCE] bg-white px-3 py-2 text-xs text-[#242321] focus:border-[#A88B5A] focus:outline-hidden">
                                    <option value="https://simulator.sandbox.midtrans.com/qris/index" @selected(str_contains(strtolower($payment->payment_type ?? ''), 'qris') || str_contains(strtolower($payment->payment_type ?? ''), 'gopay') || empty($payment->payment_type))>QRIS / GoPay / ShopeePay</option>
                                    <option value="https://simulator.sandbox.midtrans.com/bca/va/index" @selected(str_contains(strtolower($payment->payment_type ?? ''), 'bca'))>BCA Virtual Account</option>
                                    <option value="https://simulator.sandbox.midtrans.com/bni/va/index" @selected(str_contains(strtolower($payment->payment_type ?? ''), 'bni') || empty($payment->payment_type))>BNI Virtual Account</option>
                                    <option value="https://simulator.sandbox.midtrans.com/bri/va/index" @selected(str_contains(strtolower($payment->payment_type ?? ''), 'bri'))>BRI Virtual Account</option>
                                    <option value="https://simulator.sandbox.midtrans.com/mandiri/bill/index" @selected(str_contains(strtolower($payment->payment_type ?? ''), 'mandiri') || str_contains(strtolower($payment->payment_type ?? ''), 'echannel'))>Mandiri Bill Payment</option>
                                    <option value="https://simulator.sandbox.midtrans.com/permata/va/index" @selected(str_contains(strtolower($payment->payment_type ?? ''), 'permata'))>Permata Virtual Account</option>
                                    <option value="https://docs.midtrans.com/reference/test-payment-credentials" @selected(str_contains(strtolower($payment->payment_type ?? ''), 'credit_card'))>Kartu Kredit / Debit (Kredensial Uji)</option>
                                </select>
                            </div>

                            <!-- Tombol Pay / Simulasikan Pembayaran -->
                            <a id="btn-pay-simulator"
                               href="https://simulator.sandbox.midtrans.com/bni/va/index"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[#A88B5A] hover:bg-[#957849] px-6 py-3 text-xs uppercase tracking-wider font-semibold text-white transition-all shadow-xs">
                                <span>Pay / Simulasikan Pembayaran</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <p class="text-[10px] text-[#777168] text-center">
                                Tab O'Clock ini akan tetap terbuka dan otomatis mendeteksi status settlement setelah simulasi selesai.
                            </p>
                        </div>
                    @endif
                @endunless

                <!-- 3. Tombol Refresh Status Manual (Fallback) & Navigasi -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-1">
                    <form method="post" action="{{ route('customer.payment.refresh', $order) }}" class="w-full sm:w-1/2">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 rounded-full border border-[#E5DCCE] hover:border-[#A88B5A] hover:bg-[#F7F4EC] px-4 py-2.5 text-xs uppercase tracking-wider font-semibold text-[#777168] hover:text-[#242321] transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Periksa status terbaru</span>
                        </button>
                    </form>

                    <a href="{{ route('customer.orders.index') }}"
                       class="w-full sm:w-1/2 inline-flex items-center justify-center gap-1.5 rounded-full border border-[#E5DCCE] hover:border-[#242321] px-4 py-2.5 text-xs uppercase tracking-wider font-semibold text-[#242321] transition-all">
                        <span>Lihat Pesanan Saya</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- ─── Panduan Simulator Tambahan (Mode Sandbox Saja) ─── -->
            @unless($isProduction ?? config('midtrans.is_production'))
                <div class="mt-4 pt-4 border-t border-[#E5DCCE]/60 text-right">
                    <a href="https://docs.midtrans.com/reference/simulator" target="_blank" rel="noopener noreferrer" class="text-[11px] text-[#A88B5A] hover:underline inline-flex items-center gap-1">
                        <span>Dokumentasi Lengkap Simulator Midtrans Sandbox</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            @endunless
        </div>
    </div>
</div>

<script>
    (() => {
        // ─── Simulator Method Selector Dynamic Link ──────────────────────────
        const select = document.getElementById('simulator-method-select');
        const payBtn = document.getElementById('btn-pay-simulator');
        if (select && payBtn) {
            const updateLink = () => { payBtn.href = select.value; };
            select.addEventListener('change', updateLink);
            updateLink();
        }

        // ─── Live Auto-Polling (Interval 12 Detik sesuai anjuran 10-15 dtk) ───
        @if($order->payment_status === 'pending' || $order->payment_status === 'review')
            const statusUrl = {{ Illuminate\Support\Js::from(route('customer.payment.status', $order)) }};
            const toast = document.getElementById('payment-success-toast');
            const statusText = document.getElementById('payment-status-text');
            const indicator = document.getElementById('auto-polling-indicator');
            let attempts = 0;
            const maxAttempts = 75; // 75 attempts x 12s = 15 menit polling teratur

            const pollInterval = setInterval(async () => {
                attempts++;
                if (attempts > maxAttempts) {
                    clearInterval(pollInterval);
                    if (indicator) indicator.textContent = 'Batas polling selesai';
                    return;
                }

                try {
                    const res = await fetch(statusUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();

                    if (data.is_paid) {
                        clearInterval(pollInterval);
                        if (toast) toast.classList.remove('hidden');
                        if (statusText) statusText.textContent = data.status_label;
                        if (indicator) indicator.remove();

                        // Otomatis reload setelah 2 detik agar seluruh data sinkron
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else if (data.is_terminal) {
                        clearInterval(pollInterval);
                        if (statusText) statusText.textContent = data.status_label;
                        if (indicator) indicator.remove();
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                } catch (e) {
                    // Abaikan transient error selama background polling
                }
            }, 12000); // 12 detik (range 10-15 detik)
        @endif
    })();
</script>
@endsection
