<?php

namespace Tests\Feature;

use App\Filament\Widgets\StatsOverviewWidget;
use App\Models\SensorReading;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class StatsOverviewWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Permission::firstOrCreate(['name' => 'ViewAny:SensorReading', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'View:SensorReading', 'guard_name' => 'web']);

        $this->user->givePermissionTo([
            'ViewAny:SensorReading',
            'View:SensorReading',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_menampilkan_kipas_menunggu_saat_pending(): void
    {
        SensorReading::factory()->create([
            'suhu' => 33.0,
            'relay_fan' => false,
            'relay_pump' => true,
            'fan_pending' => true,
            'led_mode' => 'pompa',
        ]);

        Livewire::test(StatsOverviewWidget::class)
            ->assertStatus(200)
            ->assertSee('Menunggu')
            ->assertSee('Ditahan, pompa prioritas');
    }

    public function test_menampilkan_kipas_hidup_saat_tidak_pending(): void
    {
        SensorReading::factory()->create([
            'suhu' => 33.0,
            'relay_fan' => true,
            'relay_pump' => false,
            'fan_pending' => false,
            'led_mode' => 'kipas',
        ]);

        Livewire::test(StatsOverviewWidget::class)
            ->assertStatus(200)
            ->assertSee('Hidup')
            ->assertDontSee('Ditahan, pompa prioritas');
    }
}
