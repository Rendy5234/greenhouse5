<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi mass assignment.
     */
    protected $fillable = [
        'suhu',
        'kelembapan_udara',
        'kelembapan_tanah',
        'relay_fan',
        'relay_pump',
        'led_mode',
        'temp_on_used',
        'temp_off_used',
        'soil_dry_used',
        'soil_wet_used',
    ];

    /**
     * Cast atribut ke tipe data yang sesuai.
     */
    protected function casts(): array
    {
        return [
            'relay_fan' => 'boolean',
            'relay_pump' => 'boolean',
            'suhu' => 'decimal:2',
            'kelembapan_udara' => 'decimal:2',
            'temp_on_used' => 'decimal:2',
            'temp_off_used' => 'decimal:2',
            'soil_dry_used' => 'integer',
            'soil_wet_used' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Scope untuk filter berdasarkan mode LED.
     */
    public function scopeByMode($query, string $mode)
    {
        return $query->where('led_mode', $mode);
    }

    /**
     * Scope untuk filter berdasarkan rentang waktu.
     */
    public function scopeDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
