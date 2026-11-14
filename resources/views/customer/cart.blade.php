@extends('layouts.app')

@section('title', "Keranjang — O'Clock")

@section('content')
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <h1 class="mb-6 font-serif text-4xl font-semibold text-[#242321]">Keranjang Belanja</h1>
    @forelse ($items as $item)
        <article class="mb-3 flex flex-wrap items-center gap-4 rounded-2xl border border-[#E5DCCE] bg-white p-4">
            @if ($item->product)
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-[#F7F4EC]">
                    <x-product-photo :product="$item->product" compact />
                </div>
            @endif
            <div class="min-w-40 flex-1">
                <h2 class="font-serif text-xl font-semibold">{{ $item->product?->name ?? 'Produk telah dihapus' }}</h2>
                @if ($item->product?->is_active)
                    <p class="text-sm">Rp{{ number_format($item->product->price, 0, ',', '.') }} per unit · Stok {{ $item->product->stock }}</p>
                    <p class="text-sm font-semibold">Subtotal Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    @if ($item->quantity > $item->product->stock)
                        <p class="text-sm text-rose-700">Jumlah melebihi stok terbaru.</p>
                    @endif
                @else
                    <p class="text-sm text-rose-700">Produk tidak tersedia.</p>
                @endif
            </div>
            @if ($item->product?->is_active && $item->product->stock > 0)
                <form method="post" action="{{ route('customer.cart.update', $item) }}" class="flex items-end gap-2">
                    @csrf
                    @method('PATCH')
                    <label class="text-xs">Jumlah
                        <input name="quantity" type="number" min="1" max="{{ $item->product->stock }}" value="{{ $item->quantity }}" required class="block w-20 rounded-lg border border-[#E5DCCE] p-2 text-sm">
                    </label>
                    <button class="rounded-lg border border-[#E5DCCE] px-3 py-2 text-sm">Simpan</button>
                </form>
            @endif
            <form method="post" action="{{ route('customer.cart.destroy', $item) }}">
                @csrf
                @method('DELETE')
                <button class="px-2 py-2 text-sm text-rose-800">Hapus</button>
            </form>
        </article>
    @empty
        <p class="rounded-2xl border border-[#E5DCCE] bg-white p-6">Keranjang Anda masih kosong.</p>
    @endforelse

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
        <a class="text-sm underline" href="{{ route('catalog.index') }}">Lanjutkan belanja</a>
        @if ($items->isNotEmpty())
            <div class="flex flex-wrap items-center gap-4">
                <strong>Total sementara Rp{{ number_format($items->sum('subtotal'), 0, ',', '.') }}</strong>
                <a class="rounded-full bg-[#242321] px-6 py-3 text-sm text-white" href="{{ route('customer.checkout.show') }}">Lanjut ke Checkout</a>
            </div>
        @endif
    </div>
</section>
@endsection
