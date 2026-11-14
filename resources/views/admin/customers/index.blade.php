@extends('layouts.admin')

@section('title', 'Daftar Pelanggan')
@section('header_title', 'Data Pelanggan (Customer)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-[#777168]">
                Daftar akun pelanggan terdaftar di website O'Clock (PRD Bab 7.4). Kredensial password dilindungi secara kriptografis dan tidak ditampilkan.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, email, no telepon..."
                   class="px-4 py-2 bg-white border border-[#E6E1D7] rounded-xl text-xs text-[#242321] focus:outline-none focus:ring-1 focus:ring-[#C2A36B] w-64">
            <button type="submit" class="px-4 py-2 bg-[#242321] text-white text-xs font-semibold uppercase tracking-wider rounded-xl hover:bg-[#C2A36B] transition-all cursor-pointer">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.customers.index') }}" class="text-xs text-[#777168] hover:text-[#242321] underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-[#E6E1D7] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#E6E1D7] text-left text-sm">
                <thead class="bg-[#F8F6F0]/60 text-xs uppercase tracking-wider text-[#777168]">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Pelanggan</th>
                        <th class="px-6 py-4 font-semibold">Alamat Email</th>
                        <th class="px-6 py-4 font-semibold">No. Telepon / WhatsApp</th>
                        <th class="px-6 py-4 font-semibold">Tanggal Registrasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6E1D7]/60">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-[#F8F6F0]/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-[#242321]">{{ $customer->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-[#777168]">
                                {{ $customer->email }}
                            </td>
                            <td class="px-6 py-4 text-xs text-[#242321]">
                                {{ $customer->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-[#777168]">
                                {{ $customer->created_at->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-[#777168]">
                                Belum ada data pelanggan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-[#E6E1D7]">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
