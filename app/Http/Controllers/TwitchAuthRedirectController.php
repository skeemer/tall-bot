<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwitchAuthRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->redirectTo('https://id.twitch.tv/oauth2/authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.twitch.client_id'),
            'scope' => 'channel:manage:broadcast user:read:chat user:bot channel:bot user:write:chat',
            'redirect_uri' => str_replace('127.0.0.1', 'localhost', route('twitch.auth.callback')),
        ]));
    }
}
