<?php

namespace App\Filament\Widgets;

use App\Models\SensorReading;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SensorChartWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik 24 Jam: Suhu & Kelembapan';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '60s';

    protected ?string $maxWidth = 'full';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $now = now();
        $start = $now->copy()->subDay();

        // Get hourly averages for the last 24 hours using cross-database compatible scope
        $readings = SensorReading::hourlyAverages($start)->get();

        $labels = [];
        $suhuData = [];
        $kelembapanUdaraData = [];
        $kelembapanTanahData = [];

        /** @var Collection<int, object{ hour: string, avg_suhu: float, avg_kelembapan_udara: float, avg_kelembapan_tanah: float }> $readings */
        foreach ($readings as $reading) {
            $labels[] = Carbon::parse($reading->hour)->format('H:i');
            $suhuData[] = round($reading->avg_suhu, 1);
            $kelembapanUdaraData[] = round($reading->avg_kelembapan_udara, 1);
            $kelembapanTanahData[] = round($reading->avg_kelembapan_tanah, 1);
        }

        // If no data, provide empty arrays with at least one point
        if (empty($labels)) {
            $labels = [$now->format('H:i')];
            $suhuData = [0];
            $kelembapanUdaraData = [0];
            $kelembapanTanahData = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Suhu (°C)',
                    'data' => $suhuData,
                    'borderColor' => 'rgb(239, 68, 68)', // red-500
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Kelembapan Udara (%)',
                    'data' => $kelembapanUdaraData,
                    'borderColor' => 'rgb(59, 130, 246)', // blue-500
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
                [
                    'label' => 'Kelembapan Tanah (%)',
                    'data' => $kelembapanTanahData,
                    'borderColor' => 'rgb(34, 197, 94)', // green-500
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Waktu (24 Jam)',
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Suhu (°C)',
                    ],
                    'min' => 0,
                    'max' => 50,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Kelembapan (%)',
                    ],
                    'min' => 0,
                    'max' => 100,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
