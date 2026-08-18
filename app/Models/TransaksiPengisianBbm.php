<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPengisianBbm extends Model
{
    protected $table = 'transaksi_pengisian_bbm';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'jenis_kendaraan',
        'kendaraan_operasional_id',
        'kendaraan_gs_id',
        'master_harga_bbm_vendor_id',
        'driver',
        'kilometer',
        'jumlah_liter',
        'harga_bbm_snapshot',
        'departemen_snapshot',
        'cost_center_snapshot',
        'total_biaya',
        'keterangan',
        'tanggal_pengisian',
    ];

    protected function casts(): array
    {
        return [
            'kendaraan_gs_id' => 'integer',
            'kilometer' => 'integer',
            'jumlah_liter' => 'decimal:2',
            'harga_bbm_snapshot' => 'decimal:2',
            'total_biaya' => 'decimal:2',
            'tanggal_pengisian' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kendaraanOperasional(): BelongsTo
    {
        return $this->belongsTo(
            KendaraanOperasional::class,
            'kendaraan_operasional_id'
        );
    }

    public function kendaraanGs(): BelongsTo
    {
        return $this->belongsTo(
            KendaraanGs::class,
            'kendaraan_gs_id'
        );
    }

    public function masterHargaBbmVendor(): BelongsTo
    {
        return $this->belongsTo(
            MasterHargaBbmVendor::class,
            'master_harga_bbm_vendor_id'
        );
    }
}