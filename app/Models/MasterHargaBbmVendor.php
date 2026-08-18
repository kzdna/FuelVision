<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterHargaBbmVendor extends Model
{
    protected $table = 'master_harga_bbm_vendor';

    public $timestamps = true;

    protected $fillable = [
        'jenis_bbm',
        'harga',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function transaksiPengisianBbm(): HasMany
    {
        return $this->hasMany(TransaksiPengisianBbm::class, 'master_harga_bbm_vendor_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
}
