<?php

namespace App\Actions;

use App\Models\TwitchConnection;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class SetTwitchConnectionChannel
{
    use AsAction;

    public function handle(TwitchConnection $twitchConnection): void
    {
        $response = Http::withHeaders([
            'Authorization'=> 'Bearer '.$twitchConnection->access_token,
            'Client-Id'=> config('services.twitch.client_id'),
        ])->get('https://api.twitch.tv/helix/users');

        $twitchConnection->channel = $response->json('data.0.id');
        $twitchConnection->save();
    }
}
