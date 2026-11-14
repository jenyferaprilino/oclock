@extends('layouts.admin')

@section('title', 'Manajemen Produk')
@section('header_title', 'Katalog Jam Tangan')

@section('content')
<div class="space-y-6">
    <!-- Filter Bar & Tambah Produk Button -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, brand, SKU..."
                   class="px-4 py-2 bg-white border border-[#E6E1D7] rounded-xl text-xs text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#C2A36B] w-64">

            <select name="category_id" class="px-3 py-2 bg-white border border-[#E6E1D7] rounded-xl text-xs text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#C2A36B]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2 bg-white border border-[#E6E1D7] rounded-xl text-xs text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#C2A36B]">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-[#242321] text-white text-xs font-semibold uppercase tracking-wider rounded-xl hover:bg-[#C2A36B] transition-all cursor-pointer">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category_id', 'status']))
                <a href="{{ route('admin.products.index') }}" class="text-xs text-[#777168] hover:text-[#242321] underline">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white text-xs uppercase tracking-wider font-semibold transition-all shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Jam Tangan
        </a>
    </div>

    <!-- Table Produk -->
    <div class="bg-white rounded-2xl border border-[#E6E1D7] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E6E1D7] text-left text-sm">
                <thead class="bg-[#F8F6F0]/60 text-xs uppercase tracking-wider text-[#777168]">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Produk</th>
                        <th class="px-6 py-4 font-semibold">SKU / Brand</th>
                        <th class="px-6 py-4 font-semibold">Kategori</th>
                        <th class="px-6 py-4 font-semibold">Harga</th>
                        <th class="px-6 py-4 font-semibold">Stok Unit</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E1D7]/60">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#F8F6F0]/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-[#F8F6F0] border border-[#E6E1D7] overflow-hidden shrink-0 flex items-center justify-center">
                                        <x-product-photo :product="$product" compact />
                                    </div>
                                    <div>
                                        <div class="font-medium text-[#242321]">{{ $product->name }}</div>
                                        <div class="text-xs text-[#777168]">{{ $product->watch_type ?? 'Analog' }} • {{ $product->case_diameter ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-[#777168]">
                                <div>{{ $product->sku }}</div>
                                <div class="text-[11px] font-sans text-[#242321]">{{ $product->brand }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-[#242321]">
                                {{ $product->category->name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-[#242321]">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.products.stock', $product) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="expected_stock" value="{{ $product->stock }}">
                                    <input type="number" name="stock" value="{{ $product->stock }}" min="0" required
                                           class="w-16 px-2 py-1 bg-[#F8F6F0]/60 border border-[#E6E1D7] rounded-lg text-xs font-medium text-center focus:ring-1 focus:ring-[#C2A36B]">
                                    <button type="submit" title="Simpan Stok" class="p-1 text-[#777168] hover:text-[#C2A36B] transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-stone-100 text-[#777168] hover:bg-stone-200' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="text-xs font-semibold text-[#777168] hover:text-[#C2A36B] transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini (soft delete)? Riwayat pesanan tetap terjaga.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition-colors cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-[#777168]">
                                Tidak ada produk yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-[#E6E1D7]">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
