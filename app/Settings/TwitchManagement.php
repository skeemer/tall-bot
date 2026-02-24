<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TwitchManagement extends Settings
{
    public ?int $botConnection = null;

    public ?int $chatConnection = null;

    public static function group(): string
    {
        return 'twitch';
    }
}
