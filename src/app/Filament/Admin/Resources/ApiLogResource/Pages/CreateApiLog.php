<?php

namespace App\Filament\Admin\Resources\ApiLogResource\Pages;

use App\Filament\Admin\Resources\ApiLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApiLog extends CreateRecord
{
    protected static string $resource = ApiLogResource::class;
}
