<?php

namespace Tests\Feature;

use App\Filament\Resources\ConfigurationResource\ConfigurationResource;
use App\Models\Configuration;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ConfigurationGlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');

        $permissions = [
            'ViewAny:Configuration',
            'View:Configuration',
            'Update:Configuration',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user);
    }

    public function test_resource_terdaftar_sebagai_pencarian_global(): void
    {
        $this->assertContains(
            ConfigurationResource::class,
            Filament::getResources(),
        );

        $this->assertTrue(ConfigurationResource::canGloballySearch());
    }

    public function test_kolom_yang_dicari_dan_limit_sesuai_konfigurasi(): void
    {
        $this->assertSame(
            ['key', 'label', 'value', 'description'],
            ConfigurationResource::getGloballySearchableAttributes(),
        );

        $this->assertSame(10, ConfigurationResource::getGlobalSearchResultsLimit());
    }

    public function test_kolom_pencarian_global_menghasilkan_kategori_configurations(): void
    {
        Configuration::factory()->create([
            'key' => 'temp_on',
            'label' => 'Suhu Kipas Nyala',
        ]);

        $results = Filament::getGlobalSearchProvider()->getResults('temp_on');

        $categories = $results->getCategories();

        $this->assertArrayHasKey(ConfigurationResource::getPluralModelLabel(), $categories);
        $this->assertCount(1, $categories[ConfigurationResource::getPluralModelLabel()]);

        $this->assertSame(
            'Suhu Kipas Nyala',
            $categories[ConfigurationResource::getPluralModelLabel()]->first()->title,
        );
    }

    public function test_hasil_pencarian_global_menampilkan_judul_dan_detail_record(): void
    {
        $configuration = Configuration::factory()->create([
            'key' => 'temp_on',
            'value' => '30.0',
            'type' => 'float',
            'label' => 'Suhu Kipas Nyala',
            'description' => 'Threshold suhu untuk menyalakan kipas',
        ]);

        Configuration::factory()->create([
            'key' => 'soil_dry',
            'label' => 'Tanah Kering',
        ]);

        $results = ConfigurationResource::getGlobalSearchResults('temp_on');

        $this->assertCount(1, $results);

        $result = $results->first();

        $this->assertSame('Suhu Kipas Nyala', $result->title);
        $this->assertSame('temp_on', $result->details['Key']);
        $this->assertSame('30.0', $result->details['Nilai']);
        $this->assertSame('float', $result->details['Tipe']);
        $this->assertSame(
            ConfigurationResource::getUrl('edit', ['record' => $configuration], panel: 'admin'),
            $result->url,
        );
    }

    public function test_pencarian_global_mencari_berdasarkan_detail_bukan_hanya_judul(): void
    {
        Configuration::factory()->create([
            'key' => 'soil_wet',
            'label' => 'Tanah Basah',
            'description' => 'Threshold kelembapan tanah untuk mematikan pompa',
        ]);

        $results = ConfigurationResource::getGlobalSearchResults('pompa');

        $this->assertCount(1, $results);
        $this->assertSame('Tanah Basah', $results->first()->title);
    }

    public function test_pencarian_global_memakai_key_sebagai_judul_jika_label_kosong(): void
    {
        Configuration::factory()->create([
            'key' => 'fan_state',
            'label' => '',
        ]);

        $results = ConfigurationResource::getGlobalSearchResults('fan_state');

        $this->assertCount(1, $results);
        $this->assertSame('fan_state', $results->first()->title);
    }
}
