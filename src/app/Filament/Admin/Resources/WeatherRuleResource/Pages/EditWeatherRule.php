<?php

namespace App\Filament\Admin\Resources\WeatherRuleResource\Pages;

use App\Filament\Admin\Resources\WeatherRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeatherRule extends EditRecord
{
    protected static string $resource = WeatherRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
