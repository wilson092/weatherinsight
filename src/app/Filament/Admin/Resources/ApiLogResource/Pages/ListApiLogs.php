<?php

namespace App\Filament\Admin\Resources\ApiLogResource\Pages;

use App\Filament\Admin\Resources\ApiLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiLogs extends ListRecords
{
    protected static string $resource = ApiLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
