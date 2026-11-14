@props(['product' => null, 'brand' => null, 'compact' => false, 'credit' => false])

@php
    $path = $product?->image_path;
    $approvedPhotos = config('product_photos', []);
    $approval = $approvedPhotos[$product?->sku][$path] ?? null;
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $verified = $approval && filled($approval['source'] ?? null) && filled($approval['usage_terms'] ?? null)
        && preg_match('/\.(?:jpe?g|png|webp)\z/i', $path) && $disk->exists($path);
    $demoPhotos = config('demo_product_photos');
    $demoKey = $demoPhotos['products'][$product?->sku] ?? null;
    $demo = $demoPhotos['enabled'] && ! $verified ? ($demoPhotos['assets'][$demoKey] ?? null) : null;
    $representative = $demo && preg_match('/\.(?:jpe?g|png|webp)\z/i', $demo['path']) && $disk->exists($demo['path']);
@endphp

@if ($verified)
    <img src="{{ asset('storage/' . $path) }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-contain">
@elseif ($representative)
    <div class="relative h-full w-full">
        <img src="{{ asset('storage/' . $demo['path']) }}" alt="{{ $demo['title'] }}" loading="lazy" class="h-full w-full object-contain">
        @unless ($compact)
            <div class="absolute inset-x-0 bottom-0 rounded-sm bg-[#F7F4EC]/95 px-2 py-1 text-center text-[10px] leading-tight text-[#242321]">
                <span class="font-semibold">Foto Ilustrasi Produk</span>
                @if ($credit)
                    <span class="block">
                        <a href="{{ $demo['source'] }}" class="underline">{{ $demo['author'] }}</a>
                        · <a href="{{ $demo['license_url'] }}" class="underline">{{ $demo['license'] }}</a>
                        @if (isset($demo['changes'])) · {{ $demo['changes'] }} @endif
                    </span>
                @endif
            </div>
        @endunless
    </div>
@elseif ($compact)
    <span class="text-[10px] text-[#777168]">—<span class="sr-only">Foto belum terverifikasi</span></span>
@else
    <x-product-photo-placeholder :brand="$product?->brand ?? $brand" />
@endif
