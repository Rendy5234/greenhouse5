<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class BinaryUuidToHex implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        // Konversi binary (16 bytes) ke hex string (32 chars)
        if (is_string($value) && strlen($value) === 16) {
            return bin2hex($value);
        }

        // Jika sudah hex string, kembalikan apa adanya
        if (is_string($value) && strlen($value) === 32) {
            return $value;
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Konversi hex string (32 chars) ke binary (16 bytes) untuk storage
        if (is_string($value) && strlen($value) === 32) {
            return hex2bin($value);
        }

        // Jika sudah binary, kembalikan apa adanya
        if (is_string($value) && strlen($value) === 16) {
            return $value;
        }

        // Jika UUID object, ambil bytes
        if (method_exists($value, 'getBytes')) {
            return $value->getBytes();
        }

        return $value;
    }
}
