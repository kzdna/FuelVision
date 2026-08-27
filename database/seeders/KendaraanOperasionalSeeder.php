<?php

namespace Database\Seeders;

use App\Models\KendaraanOperasional;
use Illuminate\Database\Seeder;

class KendaraanOperasionalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode_unit' => 'UT 208',
                'plat_nomor' => 'DA 8191 JH',
                'jenis_kendaraan' => 'B/U PMC JB HDX TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT 205',
                'plat_nomor' => 'DA 8231 JI',
                'jenis_kendaraan' => 'B/U PMC JB HDX TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT 202',
                'plat_nomor' => 'DA 8232 JI',
                'jenis_kendaraan' => 'B/U PMC JB HDX TRITON',
                'departemen' => 'Service Non PMC',
                'cost_center' => '01-STI-Non PMC',
            ],
            [
                'kode_unit' => 'UT 204',
                'plat_nomor' => 'DA 8180 JI',
                'jenis_kendaraan' => 'B/U PMC JB HDX TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT 303',
                'plat_nomor' => 'DA 1076 NH',
                'jenis_kendaraan' => 'B/U PMC INNOVA ZENIX',
                'departemen' => 'Service Non PMC',
                'cost_center' => '01-STI-Non PMC',
            ],
            [
                'kode_unit' => 'UT207',
                'plat_nomor' => 'DA 8645 JI',
                'jenis_kendaraan' => 'B/U FIELD HILUX',
                'departemen' => 'Service Non PMC',
                'cost_center' => '01-STI-Non PMC',
            ],
            [
                'kode_unit' => 'UT 304',
                'plat_nomor' => 'DA 8078 JJ',
                'jenis_kendaraan' => 'LV BOX HDX TRITON',
                'departemen' => 'Spare Part',
                'cost_center' => '01-STI-WHS',
            ],
            [
                'kode_unit' => 'UT211',
                'plat_nomor' => 'DA 8754 JJ',
                'jenis_kendaraan' => 'B/U PMC NEW TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT212',
                'plat_nomor' => 'DA 8624 JK',
                'jenis_kendaraan' => 'B/U PMC NEW TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT BIS HIACE B 01',
                'plat_nomor' => 'DA 7526 JK',
                'jenis_kendaraan' => 'BUS HAICE',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'F8512',
                'plat_nomor' => 'N 7133 UI',
                'jenis_kendaraan' => 'B/U PMC BAGONG',
                'departemen' => 'Service PMC',
                'cost_center' => '01-STI-PMC',
            ],
            [
                'kode_unit' => 'UT 302',
                'plat_nomor' => 'DA 1730 YX',
                'jenis_kendaraan' => 'B/U PMC NEW TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT 301',
                'plat_nomor' => 'DA 1710 YX',
                'jenis_kendaraan' => 'B/U PMC NEW TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT 201',
                'plat_nomor' => 'DA 1611 YX',
                'jenis_kendaraan' => 'B/U PMC NEW TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
            [
                'kode_unit' => 'UT.206',
                'plat_nomor' => 'DA 8457 ZQ',
                'jenis_kendaraan' => 'B/U FIELD HILUX',
                'departemen' => 'Service Non PMC',
                'cost_center' => '01-STI-Non PMC',
            ],
            [
                'kode_unit' => 'UT.203',
                'plat_nomor' => 'DA 8291 ZO',
                'jenis_kendaraan' => 'B/U FIELD HILUX',
                'departemen' => 'Service Non PMC',
                'cost_center' => '01-STI-Non PMC',
            ],
            [
                'kode_unit' => 'UT.209',
                'plat_nomor' => 'DA 8458 ZO',
                'jenis_kendaraan' => 'B/U FIELD HILUX',
                'departemen' => 'Service Non PMC',
                'cost_center' => '01-STI-Non PMC',
            ],
            [
                'kode_unit' => 'UT 210',
                'plat_nomor' => 'DA 8054 ZR',
                'jenis_kendaraan' => 'B/U PMC NEW TRITON',
                'departemen' => 'Administration',
                'cost_center' => '01-STI-ADM',
            ],
        ];

        foreach ($data as $item) {
            KendaraanOperasional::updateOrCreate(
                [
                    'kode_unit' => $item['kode_unit'],
                ],
                [
                    'plat_nomor' => $item['plat_nomor'],
                    'jenis_kendaraan' => $item['jenis_kendaraan'],
                    'departemen' => $item['departemen'],
                    'cost_center' => $item['cost_center'],
                    'qr_code' => $item['kode_unit'],
                    'status' => true,
                ]
            );
        }
    }
}
