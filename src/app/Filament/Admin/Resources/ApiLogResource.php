<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ApiLogResource\Pages;
use App\Models\ApiLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiLogResource extends Resource
{
    protected static ?string $model = ApiLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Api Logs';

    protected static ?string $navigationGroup = 'Weather Monitoring';

    protected static ?int $navigationSort = 2;

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

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('endpoint')
                    ->limit(40),

                Tables\Columns\TextColumn::make('status_code')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'success',
                        'failed' => 'danger',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiLogs::route('/'),
        ];
    }
}