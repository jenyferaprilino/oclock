<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        // Berdasarkan PRD Bab 8.3 & Bab XV:
        // Titik asal tetap: Kota Bengkulu
        // Tarif simulasi awal:
        // - Kota Bengkulu: Rp10.000
        // - Kota Palembang: Rp25.000
        // - Jakarta Pusat: Rp35.000
        // - Kota Bandung: Rp38.000
        // - Kota Yogyakarta: Rp40.000

        $rates = [
            '1771' => 10000, // Bengkulu
            '1671' => 25000, // Palembang
            '3171' => 35000, // Jakarta Pusat
            '3273' => 38000, // Bandung
            '3471' => 40000, // Yogyakarta
            '3374' => 38000, // Semarang (opsional wilayah tercover)
            '3578' => 42000, // Surabaya (opsional wilayah tercover)
        ];

        foreach ($rates as $cityCode => $price) {
            $city = City::where('code', $cityCode)->first();
            if ($city) {
                ShippingRate::updateOrCreate(
                    ['city_id' => $city->id],
                    [
                        'price' => $price,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
