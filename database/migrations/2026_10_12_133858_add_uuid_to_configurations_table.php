<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            // Add as nullable first (SQLite requirement)
            $table->binary('uuid', 16)->nullable()->after('id');
        });

        // Generate UUID for existing records
        $configurations = DB::table('configurations')->get();
        foreach ($configurations as $config) {
            DB::table('configurations')
                ->where('id', $config->id)
                ->update(['uuid' => Str::uuid()->getBytes()]);
        }

        // Now make it NOT NULL and unique
        Schema::table('configurations', function (Blueprint $table) {
            $table->binary('uuid', 16)->unique()->nullable(false)->change()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
