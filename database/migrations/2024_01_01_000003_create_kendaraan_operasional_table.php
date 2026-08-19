<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan_operasional', function (Blueprint $table) {
            $table->bigInteger('id', true, false);
            $table->string('kode_unit', 20);
            $table->string('plat_nomor', 30);
            $table->string('departemen', 100);
            $table->string('cost_center', 100);
            $table->string('qr_code', 255);
            $table->boolean('status');
            // Matches source SQL exactly: created_at is NOT NULL with
            // DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP on this
            // table only (unlike every other table, where created_at is
            // nullable). Flagged to the user as likely a copy/paste
            // artifact in the original dump, but replicated as-is per the
            // "SQL file is source of truth" instruction.
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->nullable();

            $table->unique('kode_unit');
            $table->unique('plat_nomor');
            $table->unique('qr_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan_operasional');
    }
};
