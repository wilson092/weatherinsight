<?php

namespace App\Filament\Admin\Resources\WeatherHistoryResource\Pages;

use App\Filament\Admin\Resources\WeatherHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWeatherHistory extends CreateRecord
{
    protected static string $resource = WeatherHistoryResource::class;
}
