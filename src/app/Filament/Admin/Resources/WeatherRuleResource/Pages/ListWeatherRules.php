<?php

namespace App\Filament\Admin\Resources\WeatherRuleResource\Pages;

use App\Filament\Admin\Resources\WeatherRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeatherRules extends ListRecords
{
    protected static string $resource = WeatherRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
