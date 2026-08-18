<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_harga_bbm_vendor', function (Blueprint $table) {
            $table->bigInteger('id', true, false);
            $table->string('jenis_bbm', 50);
            $table->decimal('harga', 15, 2);
            $table->boolean('status');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->unique('jenis_bbm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_harga_bbm_vendor');
    }
};
