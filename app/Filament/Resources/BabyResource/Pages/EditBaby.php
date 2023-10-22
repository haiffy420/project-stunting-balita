<?php

namespace App\Filament\Resources\BabyResource\Pages;

use App\Filament\Resources\BabyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBaby extends EditRecord
{
    protected static string $resource = BabyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $umur = '';

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

        $record->update($data);

        return $record;
    }
}
