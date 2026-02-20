<?php

namespace App\Filament\Resources\Commands\Pages;

use App\Filament\Resources\Commands\CommandResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommand extends CreateRecord
{
    protected static string $resource = CommandResource::class;
}
