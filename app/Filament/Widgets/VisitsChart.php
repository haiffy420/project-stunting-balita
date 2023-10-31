<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class VisitsChart extends ChartWidget
{
    protected static ?string $heading = 'Kunjungan';

    protected static string $color = 'warning';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(Visit::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Kunjungan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(function (TrendValue $value) {
                return Carbon::parse($value->date)->format('F');
            }),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
