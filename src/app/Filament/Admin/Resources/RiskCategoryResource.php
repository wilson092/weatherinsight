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
                Forms\Components\TextInput::make('suhu_minimal')
                    ->label('Suhu Minimal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('suhu_maksimal')
                    ->label('Suhu Maksimal')
                    ->helperText('Kosongkan jika tidak ada batas maksimum.')
                    ->numeric(),
                Forms\Components\Textarea::make('recommendation')
                    ->label('Rekomendasi')
                    ->helperText('Rekomendasi aktivitas atau tindakan yang disarankan untuk pengguna.')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('insight')
                    ->label('Wawasan (Insight)')
                    ->helperText('Penjelasan atau wawasan tambahan mengenai kondisi cuaca terkait.')
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
                Tables\Columns\TextColumn::make('score_range')
                    ->label('Rentang Suhu')
                    ->formatStateUsing(function ($state, RiskCategory $record) {
                        if (is_null($record->suhu_maksimal)) {
                            return "≥ {$record->suhu_minimal}°C";
                        }
                        return "{$record->suhu_minimal}°C - {$record->suhu_maksimal}°C";
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