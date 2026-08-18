<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KendaraanOperasional extends Model
{
    protected $table = 'kendaraan_operasional';

    public $timestamps = true;

    protected $fillable = [
        'kode_unit',
        'plat_nomor',
        'jenis_kendaraan',
        'departemen',
        'cost_center',
        'qr_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function transaksiPengisianBbm(): HasMany
    {
        return $this->hasMany(TransaksiPengisianBbm::class, 'kendaraan_operasional_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
}
