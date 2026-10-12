<?php

namespace App\Models;

use App\Casts\BinaryUuidToHex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'uuid',
    ];

    /**
     * Atribut yang disembunyikan dari serialisasi (array/JSON).
     * uuid adalah binary (16 bytes) - jangan ikut serialisasi ke JSON.
     */
    protected $hidden = [
        'uuid',
    ];

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->getBytes();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the route key for the model in binary format.
     */
    public function getRouteKey(): string
    {
        // Gunakan getRawOriginal untuk mendapatkan binary asli dari DB (sebelum cast)
        return bin2hex($this->getRawOriginal('uuid'));
    }

    /**
     * Resolve route binding for UUID in hex format.
     * Konversi hex string kembali ke binary sebelum query database.
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        // Konversi hex UUID (32 chars) kembali ke binary (16 bytes) untuk query
        if ($field === 'uuid' && is_string($value) && strlen($value) === 32) {
            $value = hex2bin($value);
        }

        return $query->where($field, $value);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uuid' => BinaryUuidToHex::class,
        ];
    }

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
