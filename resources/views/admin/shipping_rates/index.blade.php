@extends('layouts.admin')

@section('title', 'Tarif Pengiriman')
@section('header_title', 'Manajemen Tarif Pengiriman')

@section('content')
<div class="space-y-8">
    <!-- Notice Kota Asal Pengiriman Sesuai PRD Bab 8.1 -->
    <div class="bg-white p-5 rounded-2xl border border-[#E6E1D7] flex items-center gap-4 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-[#C2A36B]/15 text-[#C2A36B] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="text-xs text-[#777168] leading-relaxed">
            <span class="font-semibold text-[#242321]">Titik Asal Toko Tetap: Kota Bengkulu.</span>
            Tarif bersifat flat nominal Rupiah per pesanan berdasarkan kota/kabupaten tujuan tanpa tergantung berat produk pada versi 2.0. Kota tanpa tarif aktif tidak dapat melakukan checkout.
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Tambah Tarif Baru -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-[#E6E1D7] shadow-sm space-y-4">
                <h3 class="font-serif text-lg font-bold text-[#242321]">Tambah Tarif Tujuan</h3>
                <form method="POST" action="{{ route('admin.shipping-rates.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="city_id" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Pilih Kota Tujuan <span class="text-rose-500">*</span>
                        </label>
                        <select id="city_id" name="city_id" required
                                class="mt-1 block w-full px-3 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-xs focus:ring-1 focus:ring-[#C2A36B]">
                            <option value="">Pilih Kota / Kabupaten</option>
                            @foreach($availableCities as $c)
                                <option value="{{ $c->id }}" {{ old('city_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->full_name }} ({{ $c->province->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('city_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Tarif Flat (Rupiah) <span class="text-rose-500">*</span>
                        </label>
                        <input id="price" name="price" type="number" min="0" required value="{{ old('price') }}" placeholder="Contoh: 35000"
                               class="mt-1 block w-full px-3 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-xs focus:ring-1 focus:ring-[#C2A36B]">
                        @error('price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                               class="w-4 h-4 rounded border-[#E6E1D7] text-[#C2A36B] focus:ring-[#C2A36B]/40">
                        <label for="is_active" class="text-xs text-[#242321] cursor-pointer">
                            Langsung aktifkan untuk checkout
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white text-xs uppercase tracking-wider font-semibold transition-all cursor-pointer">
                        Simpan Tarif
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Tarif Saat Ini -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-[#E6E1D7] overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#E6E1D7] text-left text-sm">
                        <thead class="bg-[#F8F6F0]/60 text-xs uppercase tracking-wider text-[#777168]">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Wilayah Tujuan</th>
                                <th class="px-6 py-4 font-semibold">Provinsi</th>
                                <th class="px-6 py-4 font-semibold">Tarif Flat</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E6E1D7]/60">
                            @forelse($rates as $rate)
                                <tr class="hover:bg-[#F8F6F0]/30 transition-colors">
                                    <td class="px-6 py-4 font-medium text-[#242321]">
                                        {{ $rate->city->full_name }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-[#777168]">
                                        {{ $rate->city->province->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('admin.shipping-rates.update', $rate) }}" class="flex items-center gap-1.5">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="price" value="{{ $rate->price }}" min="0" required
                                                   class="w-24 px-2 py-1 bg-[#F8F6F0]/60 border border-[#E6E1D7] rounded-lg text-xs font-semibold text-[#242321] focus:ring-1 focus:ring-[#C2A36B]">
                                            <input type="hidden" name="is_active" value="{{ $rate->is_active ? 1 : 0 }}">
                                            <button type="submit" title="Simpan Perubahan Tarif" class="p-1 text-[#777168] hover:text-[#C2A36B] transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form method="POST" action="{{ route('admin.shipping-rates.toggle-status', $rate) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all {{ $rate->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-stone-100 text-[#777168] hover:bg-stone-200' }}">
                                                {{ $rate->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.shipping-rates.destroy', $rate) }}" class="inline"
                                              onsubmit="return confirm('Hapus tarif pengiriman untuk {{ $rate->city->full_name }}?');">
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
                                    <td colspan="5" class="px-6 py-12 text-center text-[#777168]">
                                        Belum ada tarif pengiriman yang dikonfigurasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($rates->hasPages())
                    <div class="px-6 py-4 border-t border-[#E6E1D7]">
                        {{ $rates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
