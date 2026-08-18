<?php

namespace App\Services;

use App\Models\TransaksiPengisianBbm;
use Carbon\Carbon;

class DashboardService
{
    public function getSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = TransaksiPengisianBbm::query();

        if ($startDate) {
            $query->whereDate('tanggal_pengisian', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal_pengisian', '<=', $endDate);
        }

        $totalTransaksi = (clone $query)->count();

        $totalLiter = (clone $query)->sum('jumlah_liter');

        $totalBiaya = (clone $query)->sum('total_biaya');

        $rataRataLiter = $totalTransaksi > 0
            ? $totalLiter / $totalTransaksi
            : 0;

        return [
            'total_transaksi' => $totalTransaksi,
            'total_liter' => $totalLiter,
            'total_biaya' => $totalBiaya,
            'rata_rata_liter' => $rataRataLiter,
        ];
    }
}