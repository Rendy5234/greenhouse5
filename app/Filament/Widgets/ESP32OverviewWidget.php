<?php

namespace App\Filament\Widgets;

use App\Models\SensorReading;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ESP32OverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $latest = $this->getLatestSensorReading();

        if (! $latest) {
            return [
                Stat::make('Status ESP32', 'Tidak Terhubung')
                    ->description('Belum ada data dari ESP32')
                    ->descriptionIcon(Heroicon::SignalSlash)
                    ->color('danger'),

                Stat::make('Interval Rata-rata', '—')
                    ->description('Tidak tersedia')
                    ->descriptionIcon(Heroicon::ArrowPath)
                    ->color('gray'),

                Stat::make('Data Terakhir', '—')
                    ->description('Belum ada data')
                    ->descriptionIcon(Heroicon::CalendarDays)
                    ->color('gray'),
            ];
        }

        $isOnline = $this->isOnline($latest);
        $avgInterval = $this->calculateAverageInterval();
        $lastDataTime = $latest->created_at->diffForHumans();

        return [
            Stat::make('Status ESP32', $isOnline ? 'Online' : 'Offline')
                ->description($isOnline ? "Terupdate {$lastDataTime}" : "Terakhir online {$lastDataTime}")
                ->descriptionIcon($isOnline ? Heroicon::Signal : Heroicon::SignalSlash)
                ->color($isOnline ? 'success' : 'danger'),

            Stat::make('Interval Rata-rata', $avgInterval !== null ? "{$avgInterval} s" : '—')
                ->description($avgInterval !== null ? '10 data terakhir' : 'Tidak tersedia')
                ->descriptionIcon(Heroicon::ArrowPath)
                ->color($this->getIntervalColor($avgInterval)),

            Stat::make('Data Terakhir', $latest->created_at->format('d M Y H:i:s'))
                ->description("Terupdate {$lastDataTime}")
                ->descriptionIcon(Heroicon::CalendarDays)
                ->color('info'),
        ];
    }

    private function getLatestSensorReading(): ?SensorReading
    {
        return SensorReading::latest('created_at')->first();
    }

    private function isOnline(SensorReading $reading): bool
    {
        return $reading->created_at->gte(now()->subMinutes(2));
    }

    private function calculateAverageInterval(): ?float
    {
        $readings = SensorReading::latest('created_at')
            ->limit(10)
            ->get(['created_at'])
            ->reverse()
            ->values();

        if ($readings->count() < 2) {
            return null;
        }

        $totalInterval = 0;
        for ($i = 1; $i < $readings->count(); $i++) {
            $prev = Carbon::parse($readings[$i - 1]->created_at);
            $curr = Carbon::parse($readings[$i]->created_at);
            $totalInterval += $prev->diffInSeconds($curr);
        }

        return round($totalInterval / ($readings->count() - 1), 1);
    }

    private function getIntervalColor(?float $interval): string
    {
        if ($interval === null) {
            return 'gray';
        }

        if ($interval > 15) {
            return 'warning';
        }

        return 'success';
    }
}
