<?php

namespace App\Filament\Widgets;

use App\Models\SensorReading;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        $latest = SensorReading::latest('created_at')->first();

        if (! $latest) {
            return [
                Stat::make('Belum Ada Data', '—')
                    ->description('Sensor belum mengirim data')
                    ->descriptionIcon(Heroicon::ExclamationTriangle)
                    ->color('gray'),
            ];
        }

        $timeAgo = $latest->created_at->diffForHumans();

        $charts = $this->getChartData();

        return [
            Stat::make('Suhu', "{$latest->suhu} °C")
                ->description("Terupdate {$timeAgo}")
                ->descriptionIcon(Heroicon::Sun)
                ->color($this->getTemperatureColor($latest->suhu))
                ->chart($charts['suhu']),

            Stat::make('Kelembapan Udara', "{$latest->kelembapan_udara} %")
                ->description("Terupdate {$timeAgo}")
                ->descriptionIcon(Heroicon::Cloud)
                ->color($this->getHumidityColor($latest->kelembapan_udara))
                ->chart($charts['kelembapan_udara']),

            Stat::make('Kelembapan Tanah', "{$latest->kelembapan_tanah} %")
                ->description("Terupdate {$timeAgo}")
                ->descriptionIcon(Heroicon::Beaker)
                ->color($this->getSoilHumidityColor($latest->kelembapan_tanah))
                ->chart($charts['kelembapan_tanah']),

            Stat::make('Status Kipas', $latest->fan_pending ? 'Menunggu' : ($latest->relay_fan ? 'Hidup' : 'Mati'))
                ->description($latest->fan_pending ? "Ditahan, pompa prioritas · {$timeAgo}" : "Terupdate {$timeAgo}")
                ->descriptionIcon($latest->fan_pending ? Heroicon::ArrowPath : Heroicon::Bars3)
                ->color($latest->fan_pending ? 'warning' : ($latest->relay_fan ? 'success' : 'gray'))
                ->extraAttributes([
                    'class' => $latest->fan_pending
                        ? 'bg-amber-50 dark:bg-amber-900/20'
                        : ($latest->relay_fan ? 'bg-green-50 dark:bg-green-900/20' : ''),
                ]),

            Stat::make('Status Pompa', $latest->relay_pump ? 'Hidup' : 'Mati')
                ->description("Terupdate {$timeAgo}")
                ->descriptionIcon(Heroicon::Bolt)
                ->color($latest->relay_pump ? 'success' : 'gray')
                ->extraAttributes([
                    'class' => $latest->relay_pump ? 'bg-blue-50 dark:bg-blue-900/20' : '',
                ]),

            Stat::make('Mode LED', ucfirst($latest->led_mode))
                ->description("Terupdate {$timeAgo}")
                ->descriptionIcon(Heroicon::LightBulb)
                ->color($this->getLedModeColor($latest->led_mode)),
        ];
    }

    private function getTemperatureColor(float $temp): string
    {
        if ($temp >= 35) {
            return 'danger';
        }
        if ($temp >= 30) {
            return 'warning';
        }
        if ($temp <= 15) {
            return 'info';
        }

        return 'success';
    }

    private function getHumidityColor(float $humidity): string
    {
        if ($humidity >= 90) {
            return 'danger';
        }
        if ($humidity >= 80) {
            return 'warning';
        }
        if ($humidity <= 40) {
            return 'info';
        }

        return 'success';
    }

    private function getSoilHumidityColor(float $humidity): string
    {
        if ($humidity >= 90) {
            return 'danger';
        }
        if ($humidity <= 30) {
            return 'warning';
        }

        return 'success';
    }

    private function getLedModeColor(string $mode): string
    {
        return match ($mode) {
            'kritis' => 'danger',
            'kipas' => 'info',
            'pompa' => 'warning',
            default => 'success',
        };
    }

    /**
     * Ambil data chart 24 jam untuk ketiga metrik dalam satu query.
     *
     * @return array{suhu: array<int, float|string>, kelembapan_udara: array<int, float|string>, kelembapan_tanah: array<int, float|string>}
     */
    private function getChartData(): array
    {
        $rows = SensorReading::query()
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at')
            ->get(['suhu', 'kelembapan_udara', 'kelembapan_tanah']);

        if ($rows->isEmpty()) {
            return [
                'suhu' => [0],
                'kelembapan_udara' => [0],
                'kelembapan_tanah' => [0],
            ];
        }

        return [
            'suhu' => $rows->pluck('suhu')->all(),
            'kelembapan_udara' => $rows->pluck('kelembapan_udara')->all(),
            'kelembapan_tanah' => $rows->pluck('kelembapan_tanah')->all(),
        ];
    }
}
