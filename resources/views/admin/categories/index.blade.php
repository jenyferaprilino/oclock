@extends('layouts.admin')

@section('title', 'Manajemen Kategori')
@section('header_title', 'Kategori Jam Tangan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-[#777168]">Kelola kategori untuk pengelompokan produk jam tangan di katalog.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#242321] hover:bg-[#C2A36B] text-white text-xs uppercase tracking-wider font-semibold transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-[#E6E1D7] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E6E1D7] text-left text-sm">
                <thead class="bg-[#F8F6F0]/60 text-xs uppercase tracking-wider text-[#777168]">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Kategori</th>
                        <th class="px-6 py-4 font-semibold">Slug URL</th>
                        <th class="px-6 py-4 font-semibold text-center">Jumlah Produk</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E1D7]/60">
                    @forelse($categories as $category)
                        <tr class="hover:bg-[#F8F6F0]/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-[#242321]">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-4 text-[#777168] font-mono text-xs">
                                {{ $category->slug }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $category->products_count > 0 ? 'bg-[#F8F6F0] text-[#242321] border border-[#E6E1D7]' : 'bg-stone-100 text-[#777168]' }}">
                                    {{ $category->products_count }} item
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="text-xs font-semibold text-[#777168] hover:text-[#C2A36B] transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Pastikan tidak ada produk terkait.');">
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
                            <td colspan="4" class="px-6 py-12 text-center text-[#777168]">
                                Belum ada kategori jam tangan. Silakan tambahkan kategori pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-[#E6E1D7]">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
