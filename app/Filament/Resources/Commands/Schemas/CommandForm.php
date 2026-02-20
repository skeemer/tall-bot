<?php

namespace App\Filament\Resources\Commands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
                TextInput::make('syntax')
                    ->required(),
                TextInput::make('pattern')
                    ->required(),
                Toggle::make('enabled')
                    ->required(),
                TextInput::make('role'),
                TextInput::make('event')
                    ->required(),
            ]);
    }
}
