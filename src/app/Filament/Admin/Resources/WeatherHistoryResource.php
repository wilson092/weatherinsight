<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WeatherHistoryResource\Pages;
use App\Models\WeatherHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

use Illuminate\Database\Eloquent\Builder;

class WeatherHistoryResource extends Resource
{
    protected static ?string $model = WeatherHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationLabel = 'Weather Histories';

    protected static ?string $navigationGroup = 'Weather Monitoring';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('city')
                    ->required()
                    ->maxLength(255),

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

                TextInput::make('weather_main')
                    ->label('Weather'),

                TextInput::make('weather_description')
                    ->label('Description'),

                TextInput::make('weather_icon'),

                TextInput::make('risk_level'),

                Textarea::make('recommendation')
                    ->columnSpanFull(),

                Textarea::make('insight')
                    ->columnSpanFull(),

                DateTimePicker::make('recorded_at')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('recorded_at', 'desc')

            ->columns([

                TextColumn::make('city')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('temperature')
                    ->label('Temperature')
                    ->suffix(' °C')
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state > 33 => 'danger',
                        $state > 30 => 'warning',
                        default => 'success',
                    })
                    ->weight('bold'),

                TextColumn::make('humidity')
                    ->suffix(' %')
                    ->sortable(),

                TextColumn::make('pressure')
                    ->suffix(' hPa')
                    ->sortable(),

                TextColumn::make('wind_speed')
                    ->label('Wind')
                    ->suffix(' km/h')
                    ->sortable(),

                BadgeColumn::make('weather_main')
                    ->colors([
                        'primary',
                    ]),

                BadgeColumn::make('risk_level')
                    ->colors([
                        'danger' => 'HIGH',
                        'warning' => 'MEDIUM',
                        'success' => 'LOW',
                    ]),

                TextColumn::make('recommendation')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->recommendation),

                TextColumn::make('recorded_at')
                    ->label('Recorded')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

            ])

            ->filters([

                SelectFilter::make('city')
                    ->options(
                        WeatherHistory::query()
                            ->pluck('city', 'city')
                            ->toArray()
                    ),

                SelectFilter::make('risk_level')
                    ->options([
                        'LOW' => 'LOW',
                        'MEDIUM' => 'MEDIUM',
                        'HIGH' => 'HIGH',
                    ]),

                Filter::make('today')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereDate('recorded_at', now())
                    ),

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
            'index' => Pages\ListWeatherHistories::route('/'),
            'create' => Pages\CreateWeatherHistory::route('/create'),
            'edit' => Pages\EditWeatherHistory::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }   
    
}