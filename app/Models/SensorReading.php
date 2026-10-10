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
