<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RiskCategoryResource\Pages;
use App\Models\RiskCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RiskCategoryResource extends Resource
{
    protected static ?string $model = RiskCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'WEATHER RULE MANAGEMENT';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('risk_level')
                    ->label('Level Risiko')
                    ->options([
                        'low' => 'Rendah',
                        'medium' => 'Sedang',
                        'high' => 'Tinggi',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('min_temperature')
                    ->label('Suhu Minimum (°C)')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('max_temperature')
                    ->label('Suhu Maksimum (°C)')
                    ->helperText('Kosongkan jika tidak ada batas maksimum.')
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('temperature_range')
                    ->label('Rentang Suhu')
                    ->formatStateUsing(function ($state, RiskCategory $record) {
                        if (is_null($record->max_temperature)) {
                            return "≥ {$record->min_temperature}°C";
                        }
                        return "{$record->min_temperature}°C - {$record->max_temperature}°C";
                    }),
                Tables\Columns\BadgeColumn::make('risk_level')
                    ->label('Level Risiko')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiskCategories::route('/'),
            'create' => Pages\CreateRiskCategory::route('/create'),
            'edit' => Pages\EditRiskCategory::route('/{record}/edit'),
        ];
    }
}