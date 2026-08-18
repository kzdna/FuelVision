<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandarKonsumsiBbm extends Model
{
    protected $table = 'standar_konsumsi_bbm';

    protected $fillable = [
        'jenis_kendaraan',
        'standar_min_km_per_liter',
        'standar_max_km_per_liter',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'standar_min_km_per_liter' => 'decimal:2',
            'standar_max_km_per_liter' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
}