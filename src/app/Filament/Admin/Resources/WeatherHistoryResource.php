<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WeatherHistoryResource\Pages;
use App\Filament\Admin\Resources\WeatherHistoryResource\RelationManagers;
use App\Models\WeatherHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DateTimePicker;
class WeatherHistoryResource extends Resource
{
    protected static ?string $model = WeatherHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('city')
                ->required(),

            TextInput::make('temperature')
                ->numeric()
                ->required(),

            TextInput::make('humidity')
                ->numeric()
                ->required(),

            TextInput::make('pressure')
                ->numeric()
                ->required(),

            TextInput::make('wind_speed')
                ->numeric()
                ->required(),

            TextInput::make('weather_main'),

            TextInput::make('weather_description'),

            TextInput::make('weather_icon'),
            
            DateTimePicker::make('recorded_at')
                ->required(),

        ]);
}
    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('city')
                ->searchable(),

            TextColumn::make('temperature')
                ->suffix(' °C'),

            TextColumn::make('humidity')
                ->suffix(' %'),

            TextColumn::make('pressure')
                ->suffix(' hPa'),

            TextColumn::make('wind_speed')
                ->suffix(' km/h'),

            TextColumn::make('weather_main'),

            TextColumn::make('recorded_at')
                ->dateTime(),
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
            'index' => Pages\ListWeatherHistories::route('/'),
            'create' => Pages\CreateWeatherHistory::route('/create'),
            'edit' => Pages\EditWeatherHistory::route('/{record}/edit'),
        ];
    }
}
