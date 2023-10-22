<?php

namespace App\Filament\Resources\BabyResource\Pages;

use App\Filament\Resources\BabyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBaby extends CreateRecord
{
    protected static string $resource = BabyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $umur = '';

        if (isset($data['tahun']) && $data['tahun'] !== null) {
            $umur .= $data['tahun'] . ' Tahun';
        }

        if (isset($data['bulan']) && $data['bulan'] !== null) {
            if ($umur !== '') {
                $umur .= ' ';
            }
            $umur .= $data['bulan'] . ' Bulan';
        }

        $data['umur'] = $umur;

        return $data;
    }
}
