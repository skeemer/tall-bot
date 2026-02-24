<?php

namespace App\Listeners;

use App\Events\BotConnectionSettingChanged;
use App\Settings\TwitchManagement;
use Native\Desktop\Facades\ChildProcess;
use Spatie\LaravelSettings\Events\SettingsSaved;

class BotConnectionListener
{
    public function handle(SettingsSaved $event): void
    {
        if (get_class($event->settings) == TwitchManagement::class && ! ChildProcess::get('eventsub')) {
            BotConnectionSettingChanged::dispatch();
        }
    }
}
