<?php

namespace App\Filament\Resources;

use App\Filament\Fields\AgeInputField;
use App\Filament\Resources\BabyResource\Pages;
use App\Filament\Resources\BabyResource\RelationManagers;
use App\Filament\Resources\BabyResource\RelationManagers\VisitsRelationManager;
use App\Models\Baby;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BabyResource extends Resource
{
    protected static ?string $model = Baby::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $title = 'Data Balita';

    protected static ?string $navigationLabel = 'Balita';

    protected static ?string $modelLabel = 'Balita';

    protected static ?string $navigationGroup = 'Balita';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Balita')
                    ->description('Data balita yang akan dilakukan pemeriksaan')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Balita')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nik')
                            ->label('Nik')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('ibu')
                            ->label('Nama Ibu')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Fieldset::make('Umur Balita')
                            ->schema([
                                Forms\Components\TextInput::make('umur')
                                    ->hidden(),
                                Forms\Components\TextInput::make('tahun')
                                    ->label('')
                                    ->suffix('Tahun')
                                    ->numeric(),
                                Forms\Components\TextInput::make('bulan')
                                    ->label('')
                                    ->suffix('Bulan')
                                    ->numeric()
                                    ->maxValue(11)
                                    ->required(),
                            ])->columns(5)->columnSpanFull(),
                        Forms\Components\Fieldset::make('Tempat tinggal')
                            ->schema([
                                Forms\Components\TextInput::make('alamat')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('malaria')
                                    ->label('Daerah Endemis Malaria?')
                                    ->required()
                                    ->inline(false),
                            ])->columns(2)->columnSpanFull(),
                    ])->columns(4)->columnSpan(2),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('umur')
                    ->label('Umur'),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\IconColumn::make('malaria')
                    ->label('Daerah Endemis Malaria')
                    ->trueIcon('heroicon-o-plus-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->boolean(),
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
            VisitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBabies::route('/'),
            'create' => Pages\CreateBaby::route('/create'),
            'view' => Pages\ViewBaby::route('/{record}'),
            'edit' => Pages\EditBaby::route('/{record}/edit'),
        ];
    }
}
