<?php

namespace App\Filament\Resources\BabyResource\Pages;

use App\Filament\Resources\BabyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBabies extends ListRecords
{
    protected static string $resource = BabyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
