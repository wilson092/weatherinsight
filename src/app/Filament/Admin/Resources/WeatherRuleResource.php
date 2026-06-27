<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WeatherRuleResource\Pages;
use App\Filament\Admin\Resources\WeatherRuleResource\RelationManagers;
use App\Models\WeatherRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class WeatherRuleResource extends Resource
{
    protected static ?string $model = WeatherRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Aturan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('rule_type')
                    ->label('Parameter Cuaca')
                    ->options([
                        'temperature' => 'Suhu (°C)',
                        'humidity' => 'Kelembapan (%)',
                        'wind_speed' => 'Kecepatan Angin (m/s)',
                        'pressure' => 'Tekanan Udara (hPa)',
                    ])
                    ->required()
                    ->reactive(),
                Select::make('operator')
                    ->label('Operator')
                    ->options([
                        '>' => 'Lebih dari (>)',
                        '<' => 'Kurang dari (<)',
                        'between' => 'Di antara (between)',
                    ])
                    ->required()
                    ->reactive(),
                TextInput::make('threshold_value')
                    ->label('Nilai Ambang Batas')
                    ->numeric()
                    ->visible(fn (callable $get) => in_array($get('operator'), ['>', '<']))
                    ->required(fn (callable $get) => in_array($get('operator'), ['>', '<'])),
                TextInput::make('min_value')
                    ->label('Nilai Minimum')
                    ->numeric()
                    ->visible(fn (callable $get) => $get('operator') === 'between')
                    ->required(fn (callable $get) => $get('operator') === 'between'),
                TextInput::make('max_value')
                    ->label('Nilai Maksimum')
                    ->numeric()
                    ->visible(fn (callable $get) => $get('operator') === 'between')
                    ->required(fn (callable $get) => $get('operator') === 'between'),
                TextInput::make('score_weight')
                    ->label('Bobot Skor')
                    ->numeric()
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Aturan')->searchable()->sortable(),
                TextColumn::make('kondisi')->label('Kondisi'),
                TextColumn::make('score_weight')->label('Bobot Skor')->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
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
            'index' => Pages\ListWeatherRules::route('/'),
            'create' => Pages\CreateWeatherRule::route('/create'),
            'edit' => Pages\EditWeatherRule::route('/{record}/edit'),
        ];
    }
}
