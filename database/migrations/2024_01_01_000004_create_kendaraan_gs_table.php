<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan_gs', function (Blueprint $table) {
            $table->bigInteger('id', true, false);
            $table->string('kode_gs', 30);
            $table->string('plat_nomor', 20);
            $table->string('qr_code', 255);
            $table->boolean('status');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->unique('kode_gs');
            $table->unique('plat_nomor');
            $table->unique('qr_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan_gs');
    }
};
