<?php

namespace Tests\Feature;

use App\Filament\Widgets\SensorChartWidget;
use App\Models\SensorReading;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon as IlluminateCarbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SensorChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with permissions
        $this->user = User::factory()->create();

        // Create permissions if they don't exist
        Permission::firstOrCreate(['name' => 'ViewAny:SensorReading', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'View:SensorReading', 'guard_name' => 'web']);

        $this->user->givePermissionTo([
            'ViewAny:SensorReading',
            'View:SensorReading',
        ]);

        // Refresh permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_widget_renders_without_error(): void
    {
        // Create some sensor readings
        SensorReading::factory()
            ->count(5)
            ->state(new Sequence(
                fn ($sequence) => [
                    'created_at' => Carbon::now()->subHours(5)->addMinutes($sequence->index * 10),
                    'suhu' => 25 + $sequence->index,
                    'kelembapan_udara' => 60 + $sequence->index,
                    'kelembapan_tanah' => 40 + $sequence->index,
                ]
            ))
            ->create();

        // Test widget renders via Livewire
        Livewire::test(SensorChartWidget::class)
            ->assertStatus(200);
    }

    public function test_data_grouped_hourly_correctly(): void
    {
        $now = IlluminateCarbon::now();

        // Create readings across different hours - use explicit hour boundaries
        $hour1 = $now->copy()->subHours(2)->startOfHour();
        $hour2 = $now->copy()->subHours(1)->startOfHour();

        // Create 3 readings in hour 1
        SensorReading::factory()
            ->count(3)
            ->state(new Sequence(
                fn ($sequence) => [
                    'created_at' => $hour1->copy()->addMinutes($sequence->index * 10),
                    'suhu' => 25.0,
                    'kelembapan_udara' => 60.0,
                    'kelembapan_tanah' => 40.0,
                ]
            ))
            ->create();

        // Create 2 readings in hour 2
        SensorReading::factory()
            ->count(2)
            ->state(new Sequence(
                fn ($sequence) => [
                    'created_at' => $hour2->copy()->addMinutes($sequence->index * 10),
                    'suhu' => 30.0,
                    'kelembapan_udara' => 70.0,
                    'kelembapan_tanah' => 50.0,
                ]
            ))
            ->create();

        $readings = SensorReading::hourlyAverages($now->copy()->subDay())->get();

        // Should have 2 groups (2 hours)
        $this->assertCount(2, $readings);

        // Check first hour group (2 hours ago)
        $firstHour = $readings->firstWhere('hour', $hour1->format('Y-m-d H:00:00'));
        $this->assertNotNull($firstHour);
        $this->assertEquals(25.0, $firstHour->avg_suhu);
        $this->assertEquals(60.0, $firstHour->avg_kelembapan_udara);
        $this->assertEquals(40.0, $firstHour->avg_kelembapan_tanah);

        // Check second hour group (1 hour ago)
        $secondHour = $readings->firstWhere('hour', $hour2->format('Y-m-d H:00:00'));
        $this->assertNotNull($secondHour);
        $this->assertEquals(30.0, $secondHour->avg_suhu);
        $this->assertEquals(70.0, $secondHour->avg_kelembapan_udara);
        $this->assertEquals(50.0, $secondHour->avg_kelembapan_tanah);
    }

    public function test_uses_strftime_on_sqlite(): void
    {
        // Verify we're using SQLite
        $this->assertEquals('sqlite', DB::getDriverName());

        // Create test data
        SensorReading::factory()
            ->count(3)
            ->create([
                'created_at' => IlluminateCarbon::now()->subHours(2),
                'suhu' => 25.0,
                'kelembapan_udara' => 60.0,
                'kelembapan_tanah' => 40.0,
            ]);

        // Get the query SQL to verify strftime is used
        $query = SensorReading::hourlyAverages(IlluminateCarbon::now()->subDay());
        $sql = $query->toSql();

        // SQLite should use strftime
        $this->assertStringContainsString('strftime', $sql);
        $this->assertStringNotContainsString('DATE_FORMAT', $sql);
    }

    public function test_empty_state_returns_default_values(): void
    {
        // No sensor readings in database
        $readings = SensorReading::hourlyAverages(IlluminateCarbon::now()->subDay())->get();

        $this->assertEmpty($readings);
    }

    public function test_scope_respects_time_window(): void
    {
        $now = IlluminateCarbon::now();

        // Create reading 25 hours ago (outside 24h window)
        SensorReading::factory()->create([
            'created_at' => $now->copy()->subHours(25),
            'suhu' => 100.0,
        ]);

        // Create reading 12 hours ago (inside 24h window)
        SensorReading::factory()->create([
            'created_at' => $now->copy()->subHours(12),
            'suhu' => 25.0,
        ]);

        $readings = SensorReading::hourlyAverages($now->copy()->subDay())->get();

        // Should only include the 12-hour-old reading
        $this->assertCount(1, $readings);
        $this->assertEquals(25.0, $readings->first()->avg_suhu);
    }
}
