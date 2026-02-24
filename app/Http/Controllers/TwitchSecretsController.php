<?php

namespace App\Http\Controllers;

use App\Actions\TwitchConnections\GetBotConnection;
use Illuminate\Http\Request;

class TwitchSecretsController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json([
            'clientId' => config('services.twitch.client_id'),
            'clientSecret' => config('services.twitch.client_secret'),
            'channelId' => GetBotConnection::run()->twitch_user_id,
        ]);
    }
}
