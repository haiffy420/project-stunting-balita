<?php

namespace App\Filament\Widgets;

use App\Models\Baby;
use App\Models\User;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BalitaOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pengguna', User::query()->count())
                ->description('Semua pengguna')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 60, 15, 4, 40])
                ->color('success'),
            Stat::make('Balita', Baby::query()->count())
                ->description('Semua balita')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Kunjungan', Visit::query()->count())
                ->description('Semua kunjungan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
