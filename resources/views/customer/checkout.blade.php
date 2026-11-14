@extends('layouts.app')

@section('title', "Checkout — O'Clock")

@section('content')
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
    <a href="{{ route('customer.cart.index') }}" class="text-sm underline">Kembali ke keranjang</a>
    <h1 class="mb-6 mt-3 font-serif text-4xl font-semibold">Checkout</h1>
    <div class="grid items-start gap-6 lg:grid-cols-[1fr_22rem]">
        <form id="checkout-form" method="post" action="{{ route('customer.checkout.store') }}" class="rounded-2xl border border-[#E5DCCE] bg-white p-5 sm:p-7">
            @csrf
            <input type="hidden" name="checkout_key" value="{{ old('checkout_key', $checkoutKey) }}">
            <input type="hidden" name="quote" id="checkout-quote">
            <h2 class="mb-4 font-serif text-2xl">Alamat Pengiriman</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    'recipient_name' => ['Nama penerima', 'text', auth()->user()->name],
                    'recipient_phone' => ['Nomor telepon', 'tel', auth()->user()->phone],
                    'district_name' => ['Kecamatan', 'text', ''],
                    'postal_code' => ['Kode pos', 'text', ''],
                ] as $field => [$label, $type, $default])
                    <label class="text-sm font-medium">{{ $label }}
                        <input class="mt-1 block w-full rounded-lg border border-[#E5DCCE] p-2.5" required name="{{ $field }}" type="{{ $type }}" value="{{ old($field, $default) }}">
                    </label>
                @endforeach
                <label class="text-sm font-medium">Provinsi
                    <select class="mt-1 block w-full rounded-lg border border-[#E5DCCE] p-2.5" name="province_id" id="checkout-province" required>
                        <option value="">Pilih provinsi</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}" @selected((string) old('province_id') === (string) $province->id)>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-medium">Kota/kabupaten
                    <select class="mt-1 block w-full rounded-lg border border-[#E5DCCE] p-2.5" name="city_id" id="checkout-city" required disabled>
                        <option value="">Pilih kota/kabupaten</option>
                    </select>
                </label>
                @foreach (['shipping_address' => 'Alamat lengkap', 'notes' => 'Catatan (opsional)'] as $field => $label)
                    <label class="text-sm font-medium sm:col-span-2">{{ $label }}
                        <textarea class="mt-1 block w-full rounded-lg border border-[#E5DCCE] p-2.5" name="{{ $field }}" rows="2" @required($field === 'shipping_address')>{{ old($field) }}</textarea>
                    </label>
                @endforeach
            </div>
        </form>
        <aside class="rounded-2xl border border-[#E5DCCE] bg-white p-5 sm:p-6">
            <h2 class="font-serif text-2xl">Ringkasan Pesanan</h2>
            <p class="mt-1 text-sm text-[#777168]">Dikirim dari Kota Bengkulu</p>
            <div id="checkout-items" class="my-4 space-y-3 border-b border-[#E5DCCE] pb-4 text-sm">
                @foreach ($items as $item)
                    <p>{{ $item->product?->name ?? 'Produk tidak tersedia' }} × {{ $item->quantity }}
                        @if ($item->product?->is_active)
                            · Rp{{ number_format($item->product->price, 0, ',', '.') }} per unit · Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                        @endif
                    </p>
                @endforeach
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt>Subtotal</dt><dd id="checkout-subtotal">Rp{{ number_format($items->sum('subtotal'), 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt>Ongkos kirim</dt><dd id="checkout-shipping">—</dd></div>
                <div class="flex justify-between border-t border-[#E5DCCE] pt-3 font-semibold"><dt>Total</dt><dd id="checkout-total">—</dd></div>
            </dl>
            <p id="checkout-status" class="mt-3 text-sm text-[#777168]" role="status">Pilih tujuan untuk menghitung ongkir.</p>
            <button type="submit" id="checkout-confirm" form="checkout-form" disabled class="mt-4 w-full rounded-full bg-[#242321] px-4 py-3 text-sm font-semibold text-white disabled:opacity-50">Konfirmasi Pesanan</button>
            @unless ($paymentEnabled)
                <p class="mt-2 text-sm text-[#777168]">Konfirmasi tersedia setelah pembayaran diaktifkan.</p>
            @endunless
        </aside>
    </div>
</section>
<script>
    (() => {
        const cities = {{ Illuminate\Support\Js::from($cities) }};
        const province = document.getElementById('checkout-province');
        const city = document.getElementById('checkout-city');
        const form = document.getElementById('checkout-form');
        const status = document.getElementById('checkout-status');
        const shipping = document.getElementById('checkout-shipping');
        const total = document.getElementById('checkout-total');
        const subtotal = document.getElementById('checkout-subtotal');
        const items = document.getElementById('checkout-items');
        const quote = document.getElementById('checkout-quote');
        const confirm = document.getElementById('checkout-confirm');
        const paymentEnabled = @json($paymentEnabled);
        const rupiah = (amount) => 'Rp' + Number(amount).toLocaleString('id-ID');
        let request;

        async function updateQuote() {
            request?.abort();
            shipping.textContent = total.textContent = '—';
            quote.value = '';
            confirm.disabled = true;
            status.textContent = 'Pilih tujuan untuk menghitung ongkir.';
            if (!city.value) return;

            const current = new AbortController();
            request = current;
            status.textContent = 'Menghitung ongkir…';
            try {
                const response = await fetch({{ Illuminate\Support\Js::from(route('customer.checkout.quote', absolute: false)) }}, {
                    method: 'POST',
                    headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': form.elements._token.value},
                    body: JSON.stringify({province_id: province.value, city_id: city.value}),
                    signal: current.signal,
                });
                const data = await response.json();
                if (current.signal.aborted) return;
                if (!response.ok) {
                    status.textContent = data.errors?.city_id?.[0] ?? data.errors?.cart?.[0] ?? 'Ongkir belum dapat dihitung.';
                    return;
                }
                items.replaceChildren(...data.items.map((line) => {
                    const row = document.createElement('p');
                    row.textContent = line.product_name + ' × ' + line.quantity + ' · ' + rupiah(line.unit_price) + ' per unit · ' + rupiah(line.subtotal);
                    return row;
                }));
                subtotal.textContent = rupiah(data.subtotal);
                shipping.textContent = rupiah(data.shipping_cost);
                total.textContent = rupiah(data.total_amount);
                quote.value = data.quote;
                confirm.disabled = !paymentEnabled;
                status.textContent = '';
            } catch (error) {
                if (!current.signal.aborted) status.textContent = 'Ongkir belum dapat dihitung. Silakan coba kembali.';
            }
        }

        function loadCities(selected = '') {
            city.replaceChildren(new Option('Pilih kota/kabupaten', ''));
            for (const item of cities) {
                if (String(item.province_id) === province.value) city.add(new Option(item.name, item.id));
            }
            city.disabled = city.options.length === 1;
            city.value = selected;
            updateQuote();
        }

        province.addEventListener('change', () => loadCities());
        city.addEventListener('change', updateQuote);
        form.addEventListener('submit', () => { confirm.disabled = true; });
        loadCities({{ Illuminate\Support\Js::from((string) old('city_id')) }});
    })();
</script>
@endsection
