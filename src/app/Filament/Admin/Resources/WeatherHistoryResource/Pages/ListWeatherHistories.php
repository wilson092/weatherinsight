<?php

namespace App\Filament\Admin\Resources\WeatherHistoryResource\Pages;

use App\Filament\Admin\Resources\WeatherHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeatherHistories extends ListRecords
{
    protected static string $resource = WeatherHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
