<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Filament\Resources\VisitResource\RelationManagers;
use App\Models\Baby;
use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $title = 'Data Kunjungan';

    protected static ?string $navigationLabel = 'Kunjungan';

    protected static ?string $modelLabel = 'Kunjungan';

    protected static ?string $navigationGroup = 'Balita';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('baby_id')
                    ->relationship(name: 'baby', titleAttribute: 'nama')
                    ->label('Nama Balita')
                    ->required()
                    ->live()
                    ->afterStateUpdated(
                        function (Set $set, Get $get) {
                            $set('kunjungan', Visit::query()->where('baby_id', $get('baby_id'))->count() + 1);
                            $set('baby.umur', Baby::query()->where('id', $get('baby_id'))->pluck('umur')->first());
                            $set('baby.tahun', Baby::query()->where('id', $get('baby_id'))->pluck('tahun')->first());
                            $set('baby.bulan', Baby::query()->where('id', $get('baby_id'))->pluck('bulan')->first());

                            $latestVisit = Visit::where('baby_id', $get('baby_id'))
                                ->latest('tanggal_kunjungan')
                                ->first();
                            $set('berat_badan', fn () => optional($latestVisit)->berat_badan);
                            $set('tinggi_badan', fn () => optional($latestVisit)->tinggi_badan);
                            $set('lingkar_lengan', fn () => optional($latestVisit)->lingkar_lengan);
                            $set('lingkar_kepala', fn () => optional($latestVisit)->lingkar_kepala);
                            $set('suhu_badan', fn () => optional($latestVisit)->suhu_badan);
                            $set('penyakit', fn () => optional($latestVisit)->penyakit);
                            $set('keluhan', fn () => optional($latestVisit)->keluhan);
                        }
                    )
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('kunjungan')
                    ->label('Kunjungan-ke')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Wizard::make()
                    ->schema([
                        Forms\Components\Wizard\Step::make('Detail bayi')
                            ->description('Detail bayi pada kunjungan ini')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_kunjungan')
                                    ->default(function () {
                                        return Carbon::now()->format('d-m-Y');
                                    })
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
                                        Forms\Components\Hidden::make('umur')
                                            ->disabled()
                                            ->dehydrated(),
                                        Forms\Components\TextInput::make('tahun')
                                            ->label('Tahun')
                                            ->live()
                                            ->afterStateUpdated(
                                                function (Set $set, Get $get) {
                                                    if ($get('tahun') == '') {
                                                        return $set('umur', $get('bulan') . ' Bulan');
                                                    } else if ($get('bulan') == '') {
                                                        return $set('umur', $get('tahun') . ' Tahun');
                                                    } else if ($get('tahun') == '' && $get('bulan') == '') {
                                                        return $set('umur', '');
                                                    } else if ($get('tahun') != '' && $get('bulan') == '') {
                                                        return $set('umur', $get('tahun') . ' Tahun');
                                                    } else if ($get('tahun') == '' && $get('bulan') != '') {
                                                        return $set('umur', $get('bulan') . ' Bulan');
                                                    }
                                                    $set('umur', $get('tahun') . ' Tahun ' . $get('bulan') . ' Bulan');
                                                }
                                            )
                                            ->numeric(),
                                        Forms\Components\TextInput::make('bulan')
                                            ->label('Bulan')
                                            ->live()
                                            ->afterStateUpdated(
                                                function (Set $set, Get $get) {
                                                    if ($get('tahun') == '') {
                                                        return $set('umur', $get('bulan') . ' Bulan');
                                                    } else if ($get('bulan') == '') {
                                                        return $set('umur', $get('tahun') . ' Tahun');
                                                    } else if ($get('tahun') == '' && $get('bulan') == '') {
                                                        return $set('umur', '');
                                                    } else if ($get('tahun') != '' && $get('bulan') == '') {
                                                        return $set('umur', $get('tahun') . ' Tahun');
                                                    } else if ($get('tahun') == '' && $get('bulan') != '') {
                                                        return $set('umur', $get('bulan') . ' Bulan');
                                                    }
                                                    $set('umur', $get('tahun') . ' Tahun ' . $get('bulan') . ' Bulan');
                                                }
                                            )
                                            ->numeric()
                                            ->maxValue(11),
                                    ])->columns(6)->columnSpanFull(),
                                Forms\Components\Placeholder::make('note')
                                    ->label('Catatan: Data bayi akan otomatis terisi sesuai kunjungan sebelumnya (jika pernah). Silahkan diubah sesuai dengan kondisi bayi saat ini.')
                                    ->columnSpanFull(),
                            ])->columns([
                                'sm' => 3,
                                'md' => 3,
                                'xl' => 6,
                            ]),
                        // Wizard\Step::make('Detail penyakit')
                        //     ->description('Detail penyakit bayi pada kunjungan ini')
                        //     ->icon('heroicon-m-clipboard-document-list')
                        //     ->schema([
                        //         Forms\Components\TextInput::make('kunjungan')
                        //             ->numeric(),
                        //         Forms\Components\DatePicker::make('tanggal_kunjungan')
                        //             ->required(),
                        //         Forms\Components\TextInput::make('berat_badan')
                        //             ->required()
                        //             ->numeric(),
                        //         Forms\Components\TextInput::make('tinggi_badan')
                        //             ->required()
                        //             ->numeric(),
                        //         Forms\Components\TextInput::make('lingkar_lengan')
                        //             ->numeric(),
                        //         Forms\Components\TextInput::make('lingkar_kepala')
                        //             ->required()
                        //             ->numeric(),
                        //         Forms\Components\TextInput::make('suhu_badan')
                        //             ->required()
                        //             ->numeric(),

                        //     ])
                        //     ->columns(7),
                    ])->columnSpan('full'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'view' => Pages\ViewVisit::route('/{record}'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
}
