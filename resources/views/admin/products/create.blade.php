@extends('layouts.admin')

@section('title', 'Tambah Jam Tangan')
@section('header_title', 'Tambah Jam Tangan Baru')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white p-8 rounded-2xl border border-[#E6E1D7] shadow-sm">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Informasi Dasar -->
            <div>
                <h3 class="text-sm uppercase tracking-wider font-semibold text-[#242321] border-b border-[#E6E1D7] pb-2 mb-4">
                    1. Informasi Dasar Produk
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Nama Jam Tangan <span class="text-rose-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                               placeholder="Contoh: Aethel Minimalist Ivory 40mm"
                               class="mt-1 block w-full px-4 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B] @error('name') border-rose-400 @enderror">
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                                class="mt-1 block w-full px-4 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="brand" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Brand / Merek <span class="text-rose-500">*</span>
                        </label>
                        <input id="brand" name="brand" type="text" required value="{{ old('brand') }}"
                               placeholder="Contoh: O'Clock Signature"
                               class="mt-1 block w-full px-4 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                        @error('brand') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            SKU Produk (Unik) <span class="text-rose-500">*</span>
                        </label>
                        <input id="sku" name="sku" type="text" required value="{{ old('sku') }}"
                               placeholder="Contoh: OC-CS-007"
                               class="mt-1 block w-full px-4 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm font-mono focus:ring-1 focus:ring-[#C2A36B]">
                        @error('sku') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Harga (Rupiah) <span class="text-rose-500">*</span>
                        </label>
                        <input id="price" name="price" type="number" min="1" required value="{{ old('price') }}"
                               placeholder="Contoh: 450000"
                               class="mt-1 block w-full px-4 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                        @error('price') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Stok Unit <span class="text-rose-500">*</span>
                        </label>
                        <input id="stock" name="stock" type="number" min="0" required value="{{ old('stock', 10) }}"
                               class="mt-1 block w-full px-4 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                        @error('stock') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Deskripsi Produk <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="3" required
                                  placeholder="Ceritakan detail keanggunan, material, dan keunggulan jam tangan..."
                                  class="mt-1 block w-full px-4 py-2.5 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Spesifikasi Jam Tangan -->
            <div class="pt-4">
                <h3 class="text-sm uppercase tracking-wider font-semibold text-[#242321] border-b border-[#E6E1D7] pb-2 mb-4">
                    2. Spesifikasi Jam Tangan (PRD Bab 6.3 & XI)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="watch_type" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">Tipe Jam</label>
                        <input id="watch_type" name="watch_type" type="text" value="{{ old('watch_type') }}" placeholder="Analog, Chrono, Automatic"
                               class="mt-1 block w-full px-4 py-2 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                    </div>
                    <div>
                        <label for="strap_material" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">Material Strap</label>
                        <input id="strap_material" name="strap_material" type="text" value="{{ old('strap_material') }}" placeholder="Genuine Leather, Steel"
                               class="mt-1 block w-full px-4 py-2 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                    </div>
                    <div>
                        <label for="color" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">Warna / Dial</label>
                        <input id="color" name="color" type="text" value="{{ old('color') }}" placeholder="Ivory, Rose Gold, Black"
                               class="mt-1 block w-full px-4 py-2 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                    </div>
                    <div>
                        <label for="case_diameter" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">Diameter Case</label>
                        <input id="case_diameter" name="case_diameter" type="text" value="{{ old('case_diameter') }}" placeholder="40mm, 42mm"
                               class="mt-1 block w-full px-4 py-2 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                    </div>
                    <div>
                        <label for="movement_type" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">Mesin (Movement)</label>
                        <input id="movement_type" name="movement_type" type="text" value="{{ old('movement_type') }}" placeholder="Japanese Quartz, Automatic"
                               class="mt-1 block w-full px-4 py-2 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                    </div>
                    <div>
                        <label for="water_resistance" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">Ketahanan Air</label>
                        <input id="water_resistance" name="water_resistance" type="text" value="{{ old('water_resistance') }}" placeholder="30m (3 ATM), 50m"
                               class="mt-1 block w-full px-4 py-2 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-sm focus:ring-1 focus:ring-[#C2A36B]">
                    </div>
                </div>
            </div>

            <!-- Section 3: Gambar & Status -->
            <div class="pt-4">
                <h3 class="text-sm uppercase tracking-wider font-semibold text-[#242321] border-b border-[#E6E1D7] pb-2 mb-4">
                    3. Foto Produk & Status Penjualan
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="image" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                            Unggah Foto Jam Tangan <span class="text-[#777168] lowercase font-normal">(JPG, PNG, WEBP, Maks. 2MB)</span>
                        </label>
                        <input id="image" name="image" type="file" accept="image/*"
                               class="mt-2 block w-full text-xs text-[#777168] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#242321] file:text-white hover:file:bg-[#C2A36B] cursor-pointer">
                        @error('image') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-[#E6E1D7] text-[#C2A36B] focus:ring-[#C2A36B]/40">
                        <label for="is_active" class="text-xs font-semibold text-[#242321] cursor-pointer">
                            Tampilkan produk di katalog toko (Status Aktif)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-[#E6E1D7]">
                <a href="{{ route('admin.products.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-[#E6E1D7] text-xs uppercase tracking-wider font-semibold text-[#777168] hover:text-[#242321] transition-all">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white text-xs uppercase tracking-wider font-semibold transition-all cursor-pointer">
                    Simpan Jam Tangan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
