<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $provinceMap = Province::pluck('id', 'name')->toArray();

        $cities = [
            // Bengkulu (Toko Asal)
            [
                'province' => 'Bengkulu',
                'code' => '1771',
                'name' => 'Bengkulu',
                'type' => 'city',
            ],
            // Sumatera Selatan
            [
                'province' => 'Sumatera Selatan',
                'code' => '1671',
                'name' => 'Palembang',
                'type' => 'city',
            ],
            // DKI Jakarta
            [
                'province' => 'DKI Jakarta',
                'code' => '3171',
                'name' => 'Jakarta Pusat',
                'type' => 'city',
            ],
            [
                'province' => 'DKI Jakarta',
                'code' => '3173',
                'name' => 'Jakarta Barat',
                'type' => 'city',
            ],
            [
                'province' => 'DKI Jakarta',
                'code' => '3174',
                'name' => 'Jakarta Selatan',
                'type' => 'city',
            ],
            // Jawa Barat
            [
                'province' => 'Jawa Barat',
                'code' => '3273',
                'name' => 'Bandung',
                'type' => 'city',
            ],
            // DI Yogyakarta
            [
                'province' => 'DI Yogyakarta',
                'code' => '3471',
                'name' => 'Yogyakarta',
                'type' => 'city',
            ],
            // Jawa Tengah
            [
                'province' => 'Jawa Tengah',
                'code' => '3374',
                'name' => 'Semarang',
                'type' => 'city',
            ],
            // Jawa Timur
            [
                'province' => 'Jawa Timur',
                'code' => '3578',
                'name' => 'Surabaya',
                'type' => 'city',
            ],
        ];

        foreach ($cities as $item) {
            $provinceId = $provinceMap[$item['province']] ?? null;
            if ($provinceId) {
                City::updateOrCreate(
                    ['code' => $item['code']],
                    [
                        'province_id' => $provinceId,
                        'name' => $item['name'],
                        'type' => $item['type'],
                    ]
                );
            }
        }
    }
}
