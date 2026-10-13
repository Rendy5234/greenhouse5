<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->decimal('temp_on_used', 5, 2)->nullable()->after('led_mode')
                ->comment('Nilai temp_on saat reading ini masuk');
            $table->decimal('temp_off_used', 5, 2)->nullable()->after('temp_on_used')
                ->comment('Nilai temp_off saat reading ini masuk');
            $table->integer('soil_dry_used')->nullable()->after('temp_off_used')
                ->comment('Nilai soil_dry saat reading ini masuk');
            $table->integer('soil_wet_used')->nullable()->after('soil_dry_used')
                ->comment('Nilai soil_wet saat reading ini masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn(['temp_on_used', 'temp_off_used', 'soil_dry_used', 'soil_wet_used']);
        });
    }
};
