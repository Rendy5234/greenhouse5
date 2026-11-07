<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
    public function scopeByMode(Builder $query, string $mode): Builder
    {
        return $query->where('led_mode', $mode);
    }

    /**
     * Scope untuk filter berdasarkan rentang waktu.
     */
    public function scopeDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk mendapatkan rata-rata per jam (cross-database compatible).
     *
     * @param  Carbon  $since  Waktu mulai (misalnya now()->subDay())
     */
    public function scopeHourlyAverages(Builder $query, Carbon $since): Builder
    {
        return $query->selectRaw($this->getHourlySelectExpression())
            ->where('created_at', '>=', $since)
            ->groupBy('hour')
            ->orderBy('hour');
    }

    /**
     * Mendapatkan ekspresi SELECT yang kompatibel cross-database untuk grouping per jam.
     */
    private function getHourlySelectExpression(): string
    {
        $driver = DB::getDriverName();
        $dateFormat = match ($driver) {
            'sqlite' => "strftime('%Y-%m-%d %H:00:00', created_at)",
            'pgsql' => "to_char(date_trunc('hour', created_at), 'YYYY-MM-DD HH24:00:00')",
            default => "DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')", // mysql, mariadb
        };

        return "{$dateFormat} as hour, AVG(suhu) as avg_suhu, AVG(kelembapan_udara) as avg_kelembapan_udara, AVG(kelembapan_tanah) as avg_kelembapan_tanah";
    }
}
