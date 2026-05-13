<?php

namespace App\Filament\Admin\Resources\ApiLogResource\Pages;

use App\Filament\Admin\Resources\ApiLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiLog extends EditRecord
{
    protected static string $resource = ApiLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
