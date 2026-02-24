<?php

namespace App\Actions\TwitchConnections;

use App\Models\TwitchConnection;
use App\Settings\TwitchManagement;
use Lorisleiva\Actions\Concerns\AsAction;

class GetBotConnection
{
    use AsAction;

    public function __construct(private readonly TwitchManagement $settings) {}

    public function handle(): TwitchConnection
    {
        return TwitchConnection::find($this->settings->botConnection);
    }
}
