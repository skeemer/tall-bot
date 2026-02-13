<?php

namespace App\Actions;

use App\Models\TwitchConnection;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class RefreshAccessToken
{
    use AsAction;

    public function handle(TwitchConnection $twitchConnection): ?string
    {
        $response = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id' => config('services.twitch.client_id'),
            'client_secret' => config('services.twitch.client_secret'),
            'refresh_token' => $twitchConnection->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            return null;
        }

        $twitchConnection->access_token = $response->json('access_token');
        $twitchConnection->refresh_token = $response->json('refresh_token');
        if ($expiresIn = $response->json('expires_in')) {
            $twitchConnection->expires_at = now()->addSeconds($expiresIn);
        }
        $twitchConnection->save();

        return $twitchConnection->access_token;
    }
}
