<?php

namespace App\Filament\Resources\BabyResource\RelationManagers;

use App\Models\Baby;
use App\Models\Visit;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component as Livewire;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'Kunjungan';

    protected static ?string $modelLabel = 'Kunjungan';

    public function form(Form $form): Form
    {
        $latestVisit = Visit::where('baby_id', $this->ownerRecord->id)
            ->latest('tanggal_kunjungan')
            ->first();

        return $form
            ->schema([
                Forms\Components\Wizard::make()
                    ->schema([
                        Forms\Components\Wizard\Step::make('Detail Bayi')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('kunjungan')
                                    ->label('Kunjungan-ke')
                                    ->default(
                                        function () {
                                            function (Livewire $livewire) {
                                                $livewire->ownerRecord;
                                            };
                                            return Visit::query()->where('baby_id', $this->ownerRecord->id)->count() + 1;
                                        }
                                    )
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\DatePicker::make('tanggal_kunjungan')
                                    ->default(function () {
                                        return Carbon::now()->format('d-m-Y');
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('berat_badan')
                                    ->default(fn () => optional($latestVisit)->berat_badan)
                                    ->required()
                                    ->numeric()
                                    ->suffix('kg'),
                                Forms\Components\TextInput::make('tinggi_badan')
                                    ->default(fn () => optional($latestVisit)->tinggi_badan)
                                    ->required()
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('lingkar_lengan')
                                    ->default(fn () => optional($latestVisit)->lingkar_lengan)
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('lingkar_kepala')
                                    ->default(fn () => optional($latestVisit)->lingkar_kepala)
                                    ->required()
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('suhu_badan')
                                    ->default(fn () => optional($latestVisit)->suhu_badan)
                                    ->required()
                                    ->numeric()
                                    ->suffix('°C'),

                                Forms\Components\Fieldset::make('Penyakit & Keluhan')
                                    ->schema([
                                        Forms\Components\TextInput::make('penyakit')
                                            ->default(fn () => optional($latestVisit)->penyakit)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('keluhan')
                                            ->default(fn () => optional($latestVisit)->keluhan)
                                            ->maxLength(255),
                                    ])->columns(2),
                                Forms\Components\Fieldset::make('baby_id')
                                    ->relationship('baby', 'id')
                                    ->label('Umur Balita')
                                    ->schema([
                                        Forms\Components\Hidden::make('umur')
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
                                            ->default(fn ($livewire) => $livewire->ownerRecord->bulan)
                                            ->live()
                                            ->afterStateUpdated(
                                                function (Set $set, Get $get) {
                                                    if ($get('tahun') == '' && $get('bulan') == '') {
                                                        return $set('umur', '');
                                                    } else if ($get('tahun') == '') {
                                                        return $set('umur', $get('bulan') . ' Bulan');
                                                    } else if ($get('bulan') == '') {
                                                        return $set('umur', $get('tahun') . ' Tahun');
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
                                    ])->columns(7)->columnSpanFull(),
                                Forms\Components\Placeholder::make('note')
                                    ->label('Catatan: Data bayi akan otomatis terisi sesuai kunjungan sebelumnya (jika pernah). Silahkan diubah sesuai dengan kondisi bayi saat ini.')
                                    ->columnSpanFull(),
                            ])->columns([
                                'sm' => 2,
                                'md' => 4,
                                'xl' => 7,
                            ]),
                        Forms\Components\Wizard\Step::make('SAGA')
                            ->schema([
                                Forms\Components\Section::make('SAGA')
                                    ->description('Memeriksa Tanda Bahaya Umum dengan Segitiga Asesmen Gawat Anak (SAGA)')
                                    ->statePath('subform1')
                                    ->schema([
                                        Forms\Components\Toggle::make('minum_menyusu')
                                            ->label('Apakah tidak bisa minum atau menyusu?'),
                                        Forms\Components\Toggle::make('muntah')
                                            ->label('Apakah memuntahkan semua makanan dan minuman?'),
                                        Forms\Components\Toggle::make('pernah_kejang')
                                            ->label('Apakah pernah kejang selama sakit ini?'),
                                        Forms\Components\Fieldset::make('penampilan')
                                            ->label('Dari penampilan pasien maka tentukan kondisi : ')
                                            ->schema([
                                                Forms\Components\Checkbox::make('penampilan.kejang')
                                                    ->label('Kejang')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.tidak_sadar')
                                                    ->label('Tidak dapat berinteraksi dengan lingkungan atau tidak sadar')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.gelisah')
                                                    ->label('Gelisah, rewel dan tidak dapat ditenangkan')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.pandangan_kosong')
                                                    ->label('Pandangan kosong atau mata tidak membuka')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.tidak_bersuara')
                                                    ->label('Tidak bersuara atau justru menangis melengking')
                                                    ->inline(),
                                            ]),
                                        Forms\Components\Fieldset::make('usaha_nafas')
                                            ->label('Dari usaha nafas pasien maka tentukan kondisi : ')
                                            ->schema([
                                                Forms\Components\Checkbox::make('usaha_nafas.tarikan_dinding_dada')
                                                    ->label('tarikan dinding dada ke dalam')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('usaha_nafas.stridor')
                                                    ->label('Stridor')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('usaha_nafas.napas_cuping_hidung')
                                                    ->label('Napas cuping hidung')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('usaha_nafas.posisi')
                                                    ->label('Mencari posisi paling nyaman dan menolak berbaring')
                                                    ->inline(),
                                            ]),
                                        Forms\Components\Fieldset::make('sirkulasi')
                                            ->label('Dari sirkulasi pasien maka tentukan kondisi : ')
                                            ->schema([
                                                Forms\Components\Checkbox::make('sirkulasi.kejang')
                                                    ->label('Kejang')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('sirkulasi.tidak_sadar')
                                                    ->label('Pucat')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('sirkulasi.gelisah')
                                                    ->label('Tampak biru (sianosis)')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.kutis_marmorata')
                                                    ->label('Gambaran kutis marmorata (kulit seperti marmer)')
                                                    ->inline(),
                                            ]),
                                    ]),
                            ]),
                    ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                            <x-filament::button
                                type="submit"
                                size="sm"
                            >
                                Submit
                            </x-filament::button>
                        BLADE))),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        $latestVisit = Visit::where('baby_id', $this->ownerRecord->id)
            ->latest('tanggal_kunjungan')
            ->first();

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
                Tables\Actions\CreateAction::make()
                    ->steps([
                        Forms\Components\Wizard\Step::make('Detail Bayi')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('kunjungan')
                                    ->label('Kunjungan-ke')
                                    ->default(
                                        function () {
                                            function (Livewire $livewire) {
                                                $livewire->ownerRecord;
                                            };
                                            return Visit::query()->where('baby_id', $this->ownerRecord->id)->count() + 1;
                                        }
                                    )
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\DatePicker::make('tanggal_kunjungan')
                                    ->default(function () {
                                        return Carbon::now()->format('d-m-Y');
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('berat_badan')
                                    ->default(fn () => optional($latestVisit)->berat_badan)
                                    ->required()
                                    ->numeric()
                                    ->suffix('kg'),
                                Forms\Components\TextInput::make('tinggi_badan')
                                    ->default(fn () => optional($latestVisit)->tinggi_badan)
                                    ->required()
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('lingkar_lengan')
                                    ->default(fn () => optional($latestVisit)->lingkar_lengan)
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('lingkar_kepala')
                                    ->default(fn () => optional($latestVisit)->lingkar_kepala)
                                    ->required()
                                    ->numeric()
                                    ->suffix('cm'),
                                Forms\Components\TextInput::make('suhu_badan')
                                    ->default(fn () => optional($latestVisit)->suhu_badan)
                                    ->required()
                                    ->numeric()
                                    ->suffix('°C'),
                                Forms\Components\Fieldset::make('Penyakit & Keluhan')
                                    ->schema([
                                        Forms\Components\TextInput::make('penyakit')
                                            ->default(fn () => optional($latestVisit)->penyakit)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('keluhan')
                                            ->default(fn () => optional($latestVisit)->keluhan)
                                            ->maxLength(255),
                                    ])->columns(2),
                                Forms\Components\Fieldset::make('baby_id')
                                    ->relationship('baby', 'id')
                                    ->label('Umur Balita')
                                    ->schema([
                                        Forms\Components\Hidden::make('umur')
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
                                            ->default(fn ($livewire) => $livewire->ownerRecord->bulan)
                                            ->live()
                                            ->afterStateUpdated(
                                                function (Set $set, Get $get) {
                                                    if ($get('tahun') == '' && $get('bulan') == '') {
                                                        return $set('umur', '');
                                                    } else if ($get('tahun') == '') {
                                                        return $set('umur', $get('bulan') . ' Bulan');
                                                    } else if ($get('bulan') == '') {
                                                        return $set('umur', $get('tahun') . ' Tahun');
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
                                    ])->columns([
                                        'sm' => 2,
                                        'md' => 4,
                                        'xl' => 7,
                                    ]),
                                Forms\Components\Placeholder::make('note')
                                    ->label('Catatan: Data bayi akan otomatis terisi sesuai kunjungan sebelumnya (jika pernah). Silahkan diubah sesuai dengan kondisi bayi saat ini.')
                                    ->columnSpanFull(),
                            ])->columns([
                                'sm' => 2,
                                'md' => 4,
                                'xl' => 7,
                            ]),
                        Forms\Components\Wizard\Step::make('SAGA')
                            ->schema([
                                Forms\Components\Section::make('SAGA')
                                    ->description('Memeriksa Tanda Bahaya Umum dengan Segitiga Asesmen Gawat Anak (SAGA)')
                                    ->statePath('subform1')
                                    ->schema([
                                        Forms\Components\Toggle::make('minum_menyusu')
                                            ->label('Apakah tidak bisa minum atau menyusu?'),
                                        Forms\Components\Toggle::make('muntah')
                                            ->label('Apakah memuntahkan semua makanan dan minuman?'),
                                        Forms\Components\Toggle::make('pernah_kejang')
                                            ->label('Apakah pernah kejang selama sakit ini?'),
                                        Forms\Components\Fieldset::make('penampilan')
                                            ->label('Dari penampilan pasien maka tentukan kondisi : ')
                                            ->schema([
                                                Forms\Components\Checkbox::make('penampilan.kejang')
                                                    ->label('Kejang')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.tidak_sadar')
                                                    ->label('Tidak dapat berinteraksi dengan lingkungan atau tidak sadar')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.gelisah')
                                                    ->label('Gelisah, rewel dan tidak dapat ditenangkan')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.pandangan_kosong')
                                                    ->label('Pandangan kosong atau mata tidak membuka')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.tidak_bersuara')
                                                    ->label('Tidak bersuara atau justru menangis melengking')
                                                    ->inline(),
                                            ]),
                                        Forms\Components\Fieldset::make('usaha_nafas')
                                            ->label('Dari usaha nafas pasien maka tentukan kondisi : ')
                                            ->schema([
                                                Forms\Components\Checkbox::make('usaha_nafas.tarikan_dinding_dada')
                                                    ->label('tarikan dinding dada ke dalam')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('usaha_nafas.stridor')
                                                    ->label('Stridor')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('usaha_nafas.napas_cuping_hidung')
                                                    ->label('Napas cuping hidung')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('usaha_nafas.posisi')
                                                    ->label('Mencari posisi paling nyaman dan menolak berbaring')
                                                    ->inline(),
                                            ]),
                                        Forms\Components\Fieldset::make('sirkulasi')
                                            ->label('Dari sirkulasi pasien maka tentukan kondisi : ')
                                            ->schema([
                                                Forms\Components\Checkbox::make('sirkulasi.kejang')
                                                    ->label('Kejang')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('sirkulasi.tidak_sadar')
                                                    ->label('Pucat')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('sirkulasi.gelisah')
                                                    ->label('Tampak biru (sianosis)')
                                                    ->inline(),
                                                Forms\Components\Checkbox::make('penampilan.kutis_marmorata')
                                                    ->label('Gambaran kutis marmorata (kulit seperti marmer)')
                                                    ->inline(),
                                            ]),
                                    ]),
                            ]),
                        // Forms\Components\Wizard\Step::make('Kesehatan Bayi')
                        //     ->schema([
                        //         Forms\Components\Section::make('SAGA')
                        //             ->description('Memeriksa Tanda Bahaya Umum dengan Segitiga Asesmen Gawat Anak (SAGA)')
                        //             ->statePath('subform1')
                        //             ->schema([
                        //                 Forms\Components\Toggle::make('minum_menyusu')
                        //                     ->label('Apakah tidak bisa minum atau menyusu?'),
                        //             ]),
                        //     ]),
                    ])
                    ->createAnother(false),
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
