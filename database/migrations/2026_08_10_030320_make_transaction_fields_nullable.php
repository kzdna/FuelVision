<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_pengisian_bbm', function (Blueprint $table) {
            $table->bigInteger('user_id', false, false)->nullable()->change();
            $table->bigInteger('kendaraan_operasional_id', false, false)->nullable()->change();
            $table->string('departemen_snapshot', 100)->nullable()->change();
            $table->string('cost_center_snapshot', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_pengisian_bbm', function (Blueprint $table) {
            $table->bigInteger('user_id', false, false)->nullable(false)->change();
            $table->bigInteger('kendaraan_operasional_id', false, false)->nullable(false)->change();
            $table->string('departemen_snapshot', 100)->nullable(false)->change();
            $table->string('cost_center_snapshot', 100)->nullable(false)->change();
        });
    }
};