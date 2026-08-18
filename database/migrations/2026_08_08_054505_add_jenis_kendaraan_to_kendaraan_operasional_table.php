<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kendaraan_operasional', function (Blueprint $table) {
            $table->string('jenis_kendaraan', 100)
                ->nullable()
                ->after('plat_nomor');
        });
    }

    public function down(): void
    {
        Schema::table('kendaraan_operasional', function (Blueprint $table) {
            $table->dropColumn('jenis_kendaraan');
        });
    }
};