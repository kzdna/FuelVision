<?php

namespace Database\Seeders;

use App\Models\StandarKonsumsiBbm;
use Illuminate\Database\Seeder;

class StandarKonsumsiBbmSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'B/U FMC JB HDX TRITON',
            'LV BOX HDX TRITON',
            'B/U FIELD HDX TRITON',
            'B/U FIELD HILUX',
            'B/U GOH HDX TRITON',
            'LV OPS ASC HDX TRITON',
            'LV OPS P2U HILUX',
            'B/U FMC BAGONG',
            'B/U FMC JB HDX TRITON SPV',
            'BUS HAICE',
        ];

        foreach ($data as $jenisKendaraan) {
            StandarKonsumsiBbm::firstOrCreate(
                [
                    'jenis_kendaraan' => $jenisKendaraan,
                ],
                [
                    'standar_min_km_per_liter' => null,
                    'standar_max_km_per_liter' => null,
                    'status' => true,
                ]
            );
        }
    }
}