<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom fan_pending untuk menandai kipas yang ditahan
     * karena relay pompa sedang aktif (prioritas pompa).
     */
    public function up(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->boolean('fan_pending')->default(false)->after('relay_pump')
                ->comment('Kipas diminta nyala tetapi ditahan karena pompa aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn('fan_pending');
        });
    }
};
