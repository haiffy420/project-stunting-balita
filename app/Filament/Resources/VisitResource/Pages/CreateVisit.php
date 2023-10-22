<?php

namespace App\Filament\Resources\VisitResource\Pages;

use App\Filament\Resources\VisitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVisit extends CreateRecord
{
    protected static string $resource = VisitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $umur = '';
        $kunjungan = '';

        if (isset($data['tahun'])) {
            $umur .= $data['tahun'] . ' Tahun';
        }

        if (isset($data['bulan'])) {
            if ($umur !== '') {
                $umur .= ' ';
            }
            $umur .= $data['bulan'] . ' Bulan';
        }

        $data['umur'] = $umur;

        return $data;
    }
}
