<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::updateOrCreate(
            ['email' => 'admin@greenhouse.test'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign super_admin role if Shield is installed
        if (class_exists(Role::class)) {
            $superAdminRole = Role::firstOrCreate(
                ['name' => 'super_admin', 'guard_name' => 'web'],
                ['uuid' => Str::uuid()->getBytes()]
            );
            $user->assignRole($superAdminRole);
        }

        $this->call([
            ConfigurationSeeder::class,
        ]);
    }
}
