<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_pengisian_bbm', function (Blueprint $table) {
            $table->bigInteger('id', true, false);
            $table->bigInteger('user_id', false, true)->nullable();
            $table->bigInteger('kendaraan_operasional_id', false, false);
            $table->bigInteger('kendaraan_gs_id', false, false)->nullable();
            $table->bigInteger('master_harga_bbm_vendor_id', false, false);
            $table->string('driver', 100);
            $table->integer('kilometer');
            $table->decimal('jumlah_liter', 10, 2);
            $table->decimal('harga_bbm_snapshot', 15, 2);
            $table->string('departemen_snapshot', 100);
            $table->string('cost_center_snapshot', 100);
            $table->decimal('total_biaya', 15, 2);
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_pengisian');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->index('user_id');
            $table->index('kendaraan_operasional_id');
            $table->index('kendaraan_gs_id');
            $table->index('master_harga_bbm_vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pengisian_bbm');
    }
};