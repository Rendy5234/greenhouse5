<?php

namespace App\Models;

use App\Casts\BinaryUuidToHex;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
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
     * Atribut yang disembunyikan dari serialisasi (array/JSON).
     * uuid adalah binary (16 bytes) - jangan ikut serialisasi ke JSON.
     */
    protected $hidden = [
        'uuid',
    ];

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
}
