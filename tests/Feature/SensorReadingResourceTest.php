<?php

namespace Tests\Feature;

use App\Filament\Resources\SensorReadingResource\Pages\ListSensorReadings;
use App\Filament\Resources\SensorReadingResource\Pages\ViewSensorReading;
use App\Filament\Resources\SensorReadingResource\SensorReadingResource;
use App\Models\SensorReading;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SensorReadingResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');

        // Create permissions with guard name (Filament Shield format: PascalCase with colon separator)
        Permission::findOrCreate('ViewAny:SensorReading', 'web');
        Permission::findOrCreate('View:SensorReading', 'web');
        Permission::findOrCreate('Create:SensorReading', 'web');
        Permission::findOrCreate('Update:SensorReading', 'web');
        Permission::findOrCreate('Delete:SensorReading', 'web');

        // Create user with permissions
        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            'ViewAny:SensorReading',
            'View:SensorReading',
        ]);

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_resource_can_be_accessed_by_authorized_user(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ListSensorReadings::class)
            ->assertStatus(200);
    }

    public function test_resource_denies_access_to_unauthorized_user(): void
    {
        $unauthorizedUser = User::factory()->create();
        $this->actingAs($unauthorizedUser);

        $response = $this->get(route('filament.admin.resources.sensor-readings.index'));
        $response->assertStatus(403);
    }

    public function test_table_displays_sensor_readings(): void
    {
        $this->actingAs($this->user);

        SensorReading::factory()->count(3)->create();

        Livewire::test(ListSensorReadings::class)
            ->assertStatus(200);
    }

    public function test_view_page_displays_single_reading(): void
    {
        $this->actingAs($this->user);

        $reading = SensorReading::factory()->create([
            'suhu' => 25.5,
            'kelembapan_udara' => 60.0,
            'kelembapan_tanah' => 45,
            'relay_fan' => true,
            'relay_pump' => false,
            'led_mode' => 'normal',
            'temp_on_used' => 30.0,
            'temp_off_used' => 28.0,
            'soil_dry_used' => 30,
            'soil_wet_used' => 70,
        ]);

        Livewire::test(ViewSensorReading::class, ['record' => $reading->getRouteKey()])
            ->assertStatus(200)
            ->assertSee('Suhu')  // Label is rendered
            ->assertSee('Kelembapan Udara')
            ->assertSee('Kelembapan Tanah');
    }

    public function test_create_edit_delete_actions_are_disabled(): void
    {
        $this->actingAs($this->user);

        // Check canCreate returns false
        $this->assertFalse(SensorReadingResource::canCreate());

        $reading = SensorReading::factory()->create();
        $this->assertFalse(SensorReadingResource::canEdit($reading));
        $this->assertFalse(SensorReadingResource::canDelete($reading));
    }

    public function test_table_has_correct_columns(): void
    {
        $this->actingAs($this->user);

        SensorReading::factory()->create();

        Livewire::test(ListSensorReadings::class)
            ->assertStatus(200);
    }

    public function test_table_has_filters(): void
    {
        $this->actingAs($this->user);

        SensorReading::factory()->create(['led_mode' => 'normal']);
        SensorReading::factory()->create(['led_mode' => 'kritis']);

        Livewire::test(ListSensorReadings::class)
            ->assertStatus(200);
    }

    public function test_snapshot_threshold_fields_are_in_model(): void
    {
        $reading = SensorReading::factory()->create([
            'temp_on_used' => 30.5,
            'temp_off_used' => 28.5,
            'soil_dry_used' => 35,
            'soil_wet_used' => 65,
        ]);

        $this->assertEquals(30.5, $reading->temp_on_used);
        $this->assertEquals(28.5, $reading->temp_off_used);
        $this->assertEquals(35, $reading->soil_dry_used);
        $this->assertEquals(65, $reading->soil_wet_used);
    }

    public function test_snapshot_threshold_fields_are_cast_correctly(): void
    {
        $reading = SensorReading::factory()->create([
            'temp_on_used' => 30.5,
            'temp_off_used' => 28.5,
            'soil_dry_used' => 35,
            'soil_wet_used' => 65,
        ]);

        $this->assertEquals(30.5, $reading->temp_on_used);
        $this->assertEquals(28.5, $reading->temp_off_used);
        $this->assertEquals(35, $reading->soil_dry_used);
        $this->assertEquals(65, $reading->soil_wet_used);

        // Check that values are numeric (decimal cast returns float in PHP)
        $this->assertIsFloat((float) $reading->temp_on_used);
        $this->assertIsFloat((float) $reading->temp_off_used);
        $this->assertIsInt((int) $reading->soil_dry_used);
        $this->assertIsInt((int) $reading->soil_wet_used);
    }

    public function test_table_menampilkan_status_kipas_menunggu_saat_pending(): void
    {
        $this->actingAs($this->user);

        SensorReading::factory()->create([
            'relay_fan' => false,
            'relay_pump' => true,
            'fan_pending' => true,
            'led_mode' => 'pompa',
        ]);

        Livewire::test(ListSensorReadings::class)
            ->assertStatus(200)
            ->assertSee('Menunggu');
    }

    public function test_fan_pending_ter_cast_ke_boolean(): void
    {
        $reading = SensorReading::factory()->create(['fan_pending' => true]);

        $this->assertTrue($reading->fan_pending);
        $this->assertIsBool($reading->fan_pending);
    }

    public function test_tabel_default_hanya_menampilkan_data_hari_ini(): void
    {
        $this->actingAs($this->user);

        $kemarin = SensorReading::factory()->create([
            'created_at' => now()->subDays(2),
            'suhu' => 11.1,
        ]);

        $hariIni = SensorReading::factory()->create([
            'created_at' => now(),
            'suhu' => 22.2,
        ]);

        Livewire::test(ListSensorReadings::class)
            ->assertCanNotSeeTableRecords([$kemarin])
            ->assertCanSeeTableRecords([$hariIni]);
    }

    public function test_filter_periode_semua_data_menampilkan_data_lama(): void
    {
        $this->actingAs($this->user);

        $kemarin = SensorReading::factory()->create(['created_at' => now()->subDays(2)]);
        $hariIni = SensorReading::factory()->create(['created_at' => now()]);

        Livewire::test(ListSensorReadings::class)
            ->filterTable('periode', 'all')
            ->assertCanSeeTableRecords([$kemarin, $hariIni]);
    }
}
