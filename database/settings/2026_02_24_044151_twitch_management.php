<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('twitch.botConnection');
        $this->migrator->add('twitch.chatConnection');
    }
};
