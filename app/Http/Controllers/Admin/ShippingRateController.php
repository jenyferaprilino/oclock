<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingRateRequest;
use App\Http\Requests\Admin\UpdateShippingRateRequest;
use App\Models\City;
use App\Models\ShippingRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShippingRateController extends Controller
{
    public function index(): View
    {
        $rates = ShippingRate::with(['city.province'])->latest()->paginate(10);

        // Ambil kota yang belum memiliki tarif pengiriman
        $existingCityIds = ShippingRate::pluck('city_id')->toArray();
        $availableCities = City::with('province')
            ->whereNotIn('id', $existingCityIds)
            ->orderBy('name')
            ->get();

        return view('admin.shipping_rates.index', compact('rates', 'availableCities'));
    }

    public function store(StoreShippingRateRequest $request): RedirectResponse
    {
        $rate = ShippingRate::create([
            'city_id' => $request->validated('city_id'),
            'price' => $request->validated('price'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.shipping-rates.index')
            ->with('success', "Tarif pengiriman untuk {$rate->city->full_name} berhasil ditambahkan.");
    }

    public function update(UpdateShippingRateRequest $request, ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->update([
            'price' => $request->validated('price'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.shipping-rates.index')
            ->with('success', "Tarif pengiriman {$shippingRate->city->full_name} berhasil diperbarui.");
    }

    public function toggleStatus(ShippingRate $shippingRate): RedirectResponse
    {
        $shippingRate->update([
            'is_active' => ! $shippingRate->is_active,
        ]);

        $status = $shippingRate->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Tarif pengiriman {$shippingRate->city->full_name} berhasil {$status}.");
    }

    public function destroy(ShippingRate $shippingRate): RedirectResponse
    {
        $cityName = $shippingRate->city->full_name;
        $shippingRate->delete();

        return redirect()->route('admin.shipping-rates.index')
            ->with('success', "Tarif pengiriman untuk {$cityName} berhasil dihapus.");
    }
}
