<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi pembuatan tabel reading sensor.
     */
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->decimal('suhu', 5, 2);
            $table->decimal('kelembapan_udara', 5, 2);
            $table->integer('kelembapan_tanah');
            $table->boolean('relay_fan')->default(false);
            $table->boolean('relay_pump')->default(false);
            $table->string('led_mode'); // normal, kipas, pompa, kritis
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Kembalikan migrasi (hapus tabel).
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
