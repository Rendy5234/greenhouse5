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
        // Respect permission.testing config - in testing mode, keep nullable
        $isTesting = config('permission.testing');

        Schema::table('roles', function (Blueprint $table) use ($isTesting) {
            // Add as nullable first (SQLite requirement)
            $table->binary('uuid', 16)->nullable($isTesting)->after('id');
        });

        // Generate UUID for existing records
        $roles = DB::table('roles')->get();
        foreach ($roles as $role) {
            DB::table('roles')
                ->where('id', $role->id)
                ->update(['uuid' => Str::uuid()->getBytes()]);
        }

        // Now make it NOT NULL and unique (skip if testing mode)
        if (! $isTesting) {
            Schema::table('roles', function (Blueprint $table) {
                $table->binary('uuid', 16)->unique()->nullable(false)->change()->after('id');
            });
        } else {
            // In testing mode, just add unique constraint
            Schema::table('roles', function (Blueprint $table) {
                $table->unique('uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
