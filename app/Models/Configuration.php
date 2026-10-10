<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Configuration extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi mass assignment.
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'label',
        'description',
    ];

    /**
     * Relationship dengan sensor reading (kalau diperlukan nanti).
     */
    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    /**
     * Ambil nilai konfigurasi berdasarkan key dengan casting otomatis.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $config = self::where('key', $key)->first();

        if (! $config) {
            return $default;
        }

        return self::castValue($config->value, $config->type);
    }

    /**
     * Simpan nilai konfigurasi berdasarkan key.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $config = self::where('key', $key)->first();

        if ($config) {
            $config->update([
                'value' => $value,
                'type' => is_float($value) ? 'float' : 'integer',
            ]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'type' => is_float($value) ? 'float' : 'integer',
                'label' => ucfirst(str_replace('_', ' ', $key)),
                'description' => null,
            ]);
        }
    }

    /**
     * Cast value ke tipe yang sesuai berdasarkan kolom type.
     */
    private static function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'float' => (float) $value,
            'integer' => (int) $value,
            default => $value,
        };
    }
}
