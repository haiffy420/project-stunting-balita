<?php

namespace App\Filament\Widgets;

use App\Models\Baby;
use App\Models\Visit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class VisitsTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Data kunjungan';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Visit::query()
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('baby.nama')
                    ->label('Nama Balita')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('baby.ibu')
                    ->label('Nama Ibu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_kunjungan')
                    ->searchable()
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kunjungan')
                    ->label('Kunjungan-ke')
                    ->searchable()
                    ->sortable(),
            ]);
    }
}
