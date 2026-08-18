<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standar_konsumsi_bbm', function (Blueprint $table) {
            $table->string('jenis_kendaraan', 150)->after('id');
            $table->decimal('standar_min_km_per_liter', 8, 2)->nullable()->after('jenis_kendaraan');
            $table->decimal('standar_max_km_per_liter', 8, 2)->nullable()->after('standar_min_km_per_liter');
            $table->boolean('status')->default(true)->after('standar_max_km_per_liter');
        });
    }

    public function down(): void
    {
        Schema::table('standar_konsumsi_bbm', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kendaraan',
                'standar_min_km_per_liter',
                'standar_max_km_per_liter',
                'status',
            ]);
        });
    }
};