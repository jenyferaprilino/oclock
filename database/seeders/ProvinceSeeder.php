<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['code' => '17', 'name' => 'Bengkulu'],
            ['code' => '06', 'name' => 'Sumatera Selatan'],
            ['code' => '31', 'name' => 'DKI Jakarta'],
            ['code' => '32', 'name' => 'Jawa Barat'],
            ['code' => '34', 'name' => 'DI Yogyakarta'],
            ['code' => '33', 'name' => 'Jawa Tengah'],
            ['code' => '35', 'name' => 'Jawa Timur'],
            ['code' => '36', 'name' => 'Banten'],
            ['code' => '51', 'name' => 'Bali'],
            ['code' => '76', 'name' => 'Sulawesi Selatan'],
            ['code' => '12', 'name' => 'Sumatera Utara'],
            ['code' => '18', 'name' => 'Lampung'],
            ['code' => '15', 'name' => 'Jambi'],
            ['code' => '21', 'name' => 'Kepulauan Riau'],
            ['code' => '64', 'name' => 'Kalimantan Timur'],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['code' => $province['code']],
                $province
            );
        }
    }
}
