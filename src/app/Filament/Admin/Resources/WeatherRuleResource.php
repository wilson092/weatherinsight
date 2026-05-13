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
                ->required(),

            Textarea::make('conditions')
                ->required(),

            Textarea::make('recommendation')
                ->required(),

            Select::make('risk_level')
                ->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ])
                ->required(),

            Textarea::make('insight')
                ->required(),

            Toggle::make('is_active')
                ->default(true),
        ]);
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')
                ->searchable(),

            TextColumn::make('risk_level')
                ->badge(),

            TextColumn::make('recommendation')
                ->limit(30),

            IconColumn::make('is_active')
                ->boolean(),

            TextColumn::make('created_at')
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
            'index' => Pages\ListWeatherRules::route('/'),
            'create' => Pages\CreateWeatherRule::route('/create'),
            'edit' => Pages\EditWeatherRule::route('/{record}/edit'),
        ];
    }
}
