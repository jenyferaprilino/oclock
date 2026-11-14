@extends('layouts.app')

@section('title', "Invoice – {$order->order_number}")

@section('content')
<div class="bg-[#F7F4EC] min-h-screen py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div id="printable-invoice" class="bg-white rounded-2xl border border-[#E5DCCE] shadow-sm p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="font-serif text-2xl font-bold text-[#242321]">Invoice</h1>
                <button onclick="window.print()"
                        class="px-4 py-2 bg-[#A88B5A] text-white rounded hover:bg-[#8c7446] text-sm">
                    Cetak
                </button>
            </div>
            <div class="text-sm text-[#777168] mb-4">
                <strong>No. Pesanan:</strong> {{ $order->order_number }}<br>
                <strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }} WIB<br>
                <strong>Penerima:</strong> {{ $order->recipient_name }} ({{ $order->recipient_phone }})<br>
                <strong>Alamat:</strong> {{ $order->shipping_address }}, {{ $order->district_name }}, {{ $order->city_name }}, {{ $order->province_name }}, {{ $order->postal_code }}
            </div>
            @if(in_array($order->order_status, ['shipped','completed']))
                <div class="text-sm text-[#777168] mb-4">
                    <strong>Kurir:</strong> {{ $order->courier_name ?? '-' }}<br>
                    <strong>No. Resi:</strong> {{ $order->tracking_number ?? '-' }}
                </div>
            @endif
            <table class="w-full table-auto border-collapse mb-6">
                <thead>
                    <tr class="bg-[#F8F6F0]">
                        <th class="px-4 py-2 text-left text-xs uppercase text-[#777168]">Produk</th>
                        <th class="px-4 py-2 text-right text-xs uppercase text-[#777168]">Qty</th>
                        <th class="px-4 py-2 text-right text-xs uppercase text-[#777168]">Harga</th>
                        <th class="px-4 py-2 text-right text-xs uppercase text-[#777168]">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b border-[#E5DCCE]/30">
                            <td class="px-4 py-2 text-sm text-[#242321]">{{ $item->product_name }} ({{ $item->product_sku }})</td>
                            <td class="px-4 py-2 text-right text-sm text-[#242321]">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right text-sm text-[#242321]">Rp{{ number_format($item->unit_price,0,',','.') }}</td>
                            <td class="px-4 py-2 text-right text-sm text-[#242321]">Rp{{ number_format($item->subtotal,0,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-right text-sm text-[#777168] mb-2">
                <strong>Ongkos Kirim:</strong> Rp{{ number_format($order->shipping_cost,0,',','.') }}
            </div>
            <div class="text-right text-lg font-bold text-[#242321]">
                Total: Rp{{ number_format($order->total_amount,0,',','.') }}
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 12mm;
    }

    body,
    main {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    body * {
        visibility: hidden !important;
    }

    #printable-invoice,
    #printable-invoice * {
        visibility: visible !important;
    }

    #printable-invoice {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }

    #printable-invoice button {
        display: none !important;
    }
}
</style>
@endsection