<?php

namespace App\Actions;

use App\Models\TwitchConnection;
use Lorisleiva\Actions\Concerns\AsAction;

class FillTwitchConnectionInformation
{
    use AsAction;

    public function handle(TwitchConnection $twitchConnection): void
    {
        $response = GetTwitchRequestClient::run($twitchConnection)->get('/users');

        $twitchConnection->display_name = $response->json('data.0.display_name');
        $twitchConnection->twitch_user_id = $response->json('data.0.id');
        $twitchConnection->save();
    }
}
