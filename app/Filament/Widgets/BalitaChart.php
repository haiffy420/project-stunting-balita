<?php

namespace App\Filament\Widgets;

use App\Models\Baby;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BalitaChart extends ChartWidget
{
    protected static ?string $heading = 'Balita';

    protected static string $color = 'warning';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(Baby::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Balita',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
