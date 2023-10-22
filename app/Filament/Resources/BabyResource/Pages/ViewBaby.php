<?php

namespace App\Filament\Resources\BabyResource\Pages;

use App\Filament\Resources\BabyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBaby extends ViewRecord
{
    protected static string $resource = BabyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
