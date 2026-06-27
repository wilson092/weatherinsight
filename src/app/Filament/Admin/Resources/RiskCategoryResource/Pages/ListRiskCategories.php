<?php

namespace App\Filament\Admin\Resources\RiskCategoryResource\Pages;

use App\Filament\Admin\Resources\RiskCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiskCategories extends ListRecords
{
    protected static string $resource = RiskCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
