<?php

namespace App\Filament\Admin\Resources\TrackedCityResource\Pages;

use App\Filament\Admin\Resources\TrackedCityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrackedCity extends EditRecord
{
    protected static string $resource = TrackedCityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
