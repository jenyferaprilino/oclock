@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('header_title', 'Ubah Kategori: ' . $category->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white p-8 rounded-2xl border border-[#E6E1D7] shadow-sm">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <div class="mt-2">
                    <input id="name" name="name" type="text" required value="{{ old('name', $category->name) }}"
                           class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] text-sm focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all @error('name') border-rose-400 @enderror">
                </div>
                @error('name')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-xs uppercase tracking-wider font-semibold text-[#242321]">
                    Slug URL
                </label>
                <div class="mt-2">
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug) }}"
                           class="block w-full px-4 py-3 bg-[#F8F6F0]/40 border border-[#E6E1D7] rounded-xl text-[#242321] text-sm focus:outline-none focus:ring-2 focus:ring-[#C2A36B]/50 focus:border-[#C2A36B] transition-all font-mono text-xs @error('slug') border-rose-400 @enderror">
                </div>
                @error('slug')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E6E1D7]">
                <a href="{{ route('admin.categories.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-[#E6E1D7] text-xs uppercase tracking-wider font-semibold text-[#777168] hover:text-[#242321] transition-all">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white text-xs uppercase tracking-wider font-semibold transition-all cursor-pointer">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
