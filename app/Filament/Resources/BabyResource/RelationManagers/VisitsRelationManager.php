<?php

namespace App\Filament\Resources\BabyResource\RelationManagers;

use App\Models\Baby;
use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component as Livewire;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'Kunjungan';

    protected static ?string $modelLabel = 'Kunjungan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->schema([
                        Wizard\Step::make('Detail bayi')
                            ->description('Detail bayi pada kunjungan ini')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('kunjungan')
                                    ->label('Kunjungan-ke')
                                    ->default(
                                        function () {
                                            function (Livewire $livewire) {
                                                $livewire->ownerRecord;
                                            };
                                            return \App\Models\Visit::query()->where('baby_id', $this->ownerRecord->id)->count() + 1;
                                        }
                                    )
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\DatePicker::make('tanggal_kunjungan')
                                    ->required(),
                                Forms\Components\TextInput::make('berat_badan')
                                    ->required()
                                    ->numeric()
                                    ->suffix('kg'),
                                Forms\Components\TextInput::make('tinggi_badan')
                                    ->required()
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('lingkar_lengan')
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('lingkar_kepala')
                                    ->required()
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('suhu_badan')
                                    ->required()
                                    ->numeric()
                                    ->suffix('°C'),
                                Forms\Components\Fieldset::make('Penyakit & Keluhan')
                                    ->schema([
                                        Forms\Components\TextInput::make('penyakit')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('keluhan')
                                            ->maxLength(255),
                                    ])->columns(2),
                                Forms\Components\Fieldset::make('baby_id')
                                    ->relationship('baby', 'id')
                                    ->label('Umur Balita')
                                    ->schema([
                                        Forms\Components\TextInput::make('umur')
                                            // ->hidden()
                                            ->default(fn ($livewire) => $livewire->ownerRecord->umur)
                                            ->disabled()
                                            ->dehydrated(),
                                        Forms\Components\TextInput::make('tahun')
                                            ->label('Tahun')
                                            ->default(fn ($livewire) => $livewire->ownerRecord->tahun)
                                            ->live()
                                            ->afterStateUpdated(
                                                function (Set $set, Get $get) {
                                                    if ($get('tahun') == '') {
                                                        $set('tahun', 0);
                                                    }
                                                    $set('umur', $get('tahun') . ' Tahun ' . $get('bulan') . ' Bulan');
                                                }
                                            )
                                            ->numeric(),
                                        Forms\Components\TextInput::make('bulan')
                                            ->label('Bulan')
                                            ->default(fn ($livewire) => $livewire->ownerRecord->bulan)
                                            ->live()
                                            ->afterStateUpdated(
                                                function (Set $set, Get $get) {
                                                    if ($get('bulan') == '') {
                                                        $set('bulan', 0);
                                                    }
                                                    $set('umur', $get('tahun') . ' Tahun ' . $get('bulan') . ' Bulan');
                                                }
                                            )
                                            ->numeric()
                                            ->maxValue(11),
                                    ])->columns(7)->columnSpanFull(),
                            ])->columns(7),
                    ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kunjungan')
            ->columns([
                Tables\Columns\TextColumn::make('baby.nama')
                    ->label('Nama balita')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_kunjungan')
                    ->sortable()
                    ->date('d F Y'),
                Tables\Columns\TextColumn::make('kunjungan')
                    ->label('Kunjungan-ke')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('penyakit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keluhan')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
