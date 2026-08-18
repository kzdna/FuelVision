<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KendaraanGs extends Model
{
    protected $table = 'kendaraan_gs';

    public $timestamps = true;

    protected $fillable = [
        'kode_gs',
        'plat_nomor',
        'qr_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    // Intentionally no departemen/cost_center columns/attributes here:
    // kendaraan_gs does not own a Departemen or Cost Center. These are
    // always taken from the Kendaraan Operasional selected as
    // "yang digantikan" at transaction time.

    public function transaksiPengisianBbm(): HasMany
    {
        return $this->hasMany(TransaksiPengisianBbm::class, 'kendaraan_gs_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
}
