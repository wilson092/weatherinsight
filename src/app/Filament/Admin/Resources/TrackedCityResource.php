<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TrackedCityResource\Pages;
use App\Filament\Admin\Resources\TrackedCityResource\RelationManagers;
use App\Models\TrackedCity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class TrackedCityResource extends Resource
{
    protected static ?string $model = TrackedCity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
           ->columns([

    TextColumn::make('city')
        ->searchable()
        ->sortable()
        ->badge(),

    TextColumn::make('created_at')
        ->label('Added At')
        ->dateTime('d M Y H:i'),

    TextColumn::make('updated_at')
        ->label('Last Update')
        ->since(),
    TextColumn::make('histories_count')
        ->counts('histories')
        ->label('total records'),

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
            'index' => Pages\ListTrackedCities::route('/'),
            'create' => Pages\CreateTrackedCity::route('/create'),
            'edit' => Pages\EditTrackedCity::route('/{record}/edit'),
        ];
    }
}
