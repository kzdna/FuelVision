<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPengisianBbm;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalTransaksi = TransaksiPengisianBbm::count();

        $totalLiter = TransaksiPengisianBbm::sum('jumlah_liter');

        $totalBiaya = TransaksiPengisianBbm::sum('total_biaya');

        $rataRataLiter = $totalTransaksi > 0
            ? $totalLiter / $totalTransaksi
            : 0;

        $transaksiTerbaru = TransaksiPengisianBbm::with([
            'kendaraanOperasional',
            'kendaraanGs',
            'masterHargaBbmVendor',
        ])
            ->latest('tanggal_pengisian')
            ->latest('id')
            ->limit(5)
            ->get();

        $perJenisKendaraan = TransaksiPengisianBbm::with([
            'kendaraanOperasional',
        ])
            ->get()
            ->groupBy(function ($item) {
                return $item->kendaraanOperasional->jenis_kendaraan ?? 'Kendaraan GS';
            })
            ->map(function ($items) {
                return [
                    'total_transaksi' => $items->count(),
                    'total_liter' => $items->sum('jumlah_liter'),
                    'total_biaya' => $items->sum('total_biaya'),
                ];
            });

        $perKendaraan = TransaksiPengisianBbm::with([
            'kendaraanOperasional',
        ])
            ->get()
            ->groupBy(function ($item) {
                return $item->kendaraanOperasional->kode_unit ?? 'Kendaraan GS';
            })
            ->map(function ($items) {
                return [
                    'kode_unit' => $items->first()->kendaraanOperasional->kode_unit ?? 'Kendaraan GS',
                    'jenis_kendaraan' => $items->first()->kendaraanOperasional->jenis_kendaraan ?? 'Kendaraan GS',
                    'total_transaksi' => $items->count(),
                    'total_liter' => $items->sum('jumlah_liter'),
                    'total_biaya' => $items->sum('total_biaya'),
                ];
            })
            ->sortByDesc('total_liter');

        $kendaraanTertinggi = $perKendaraan->first();

        return view('dashboard.index', [
            'totalTransaksi' => $totalTransaksi,
            'totalLiter' => $totalLiter,
            'totalBiaya' => $totalBiaya,
            'rataRataLiter' => $rataRataLiter,
            'transaksiTerbaru' => $transaksiTerbaru,
            'perJenisKendaraan' => $perJenisKendaraan,
            'perKendaraan' => $perKendaraan,
            'kendaraanTertinggi' => $kendaraanTertinggi,
        ]);
    }
}