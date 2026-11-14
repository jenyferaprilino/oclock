@props(['product'])

<div class="group bg-white rounded-2xl border border-[#E5DCCE] overflow-hidden flex flex-col transition-all duration-300 hover:border-[#A88B5A]/70 hover:shadow-sm">
    <!-- Image Container with Uniform Ratio & Clean Padding -->
    <a href="{{ route('catalog.show', $product->slug) }}" aria-label="{{ $product->name }}" class="relative block aspect-square bg-[#F7F4EC] px-6 py-10 overflow-hidden">
        <x-product-photo :product="$product" />

        <!-- Minimalist Category Badge -->
        <div class="absolute top-3.5 left-3.5 z-10">
            @if($product->category)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium tracking-wider uppercase bg-white/95 text-[#242321] border border-[#E5DCCE] shadow-2xs">
                    {{ $product->category->name }}
                </span>
            @endif
        </div>

        <!-- Minimalist Stock Indicator -->
        <div class="absolute top-3.5 right-3.5 z-10">
            @if($product->stock > 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-white/95 text-emerald-800 border border-emerald-100 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Stok {{ $product->stock }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-stone-100/90 text-stone-500 border border-stone-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>
                    Habis
                </span>
            @endif
        </div>
    </a>

    <!-- Card Body Content -->
    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
        <div class="space-y-1.5">
            <!-- Brand & Movement/Type -->
            <div class="flex items-center justify-between text-xs text-[#777168]">
                <span class="uppercase tracking-widest font-semibold text-[10px] text-[#A88B5A]">{{ $product->brand }}</span>
                <span class="text-[11px] font-light">{{ $product->watch_type ?? 'Analog' }}</span>
            </div>

            <!-- Model Title -->
            <h3 class="font-serif text-base sm:text-lg font-semibold text-[#242321] group-hover:text-[#A88B5A] transition-colors leading-snug line-clamp-2">
                <a href="{{ route('catalog.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Subtle Spec Line -->
            <p class="text-xs text-[#777168] line-clamp-1 font-light pt-0.5">
                {{ $product->case_diameter ?? 'Diameter N/A' }} • {{ $product->strap_material ?? 'Strap N/A' }}
            </p>
        </div>

        <!-- Footer / Price & Clean Action -->
        <div class="mt-5 pt-4 border-t border-[#E5DCCE]/70 flex items-center justify-between">
            <div>
                <span class="block text-[9px] uppercase tracking-widest text-[#777168] font-medium">Harga Simulasi</span>
                <span class="font-sans text-base sm:text-lg font-semibold text-[#242321]">
                    Rp{{ number_format($product->price, 0, ',', '.') }}
                </span>
            </div>

            <a href="{{ route('catalog.show', $product->slug) }}"
               class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full bg-[#F7F4EC] hover:bg-[#242321] text-[#242321] hover:text-white border border-[#E5DCCE] hover:border-[#242321] text-xs font-medium tracking-wide transition-all group/btn">
                <span>Detail</span>
                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
