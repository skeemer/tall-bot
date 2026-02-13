<?php

namespace App\Actions;

use App\Models\TwitchConnection;
use Http;
use Illuminate\Http\Client\PendingRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetTwitchRequestClient
{
    use AsAction;

    public function handle(TwitchConnection $twitchConnection): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer '.$twitchConnection->access_token,
            'Client-Id' => config('services.twitch.client_id'),
        ])
            ->baseUrl(config('services.twitch.http_url'))
            ->retry(2, when: function ($exception, PendingRequest $http) use ($twitchConnection) {
                if ($exception->getCode() !== 401) {
                    return false;
                }

                $token = RefreshAccessToken::run($twitchConnection);
                if (! $token) {
                    return false;
                }

                $http->replaceHeaders(['Authorization' => 'Bearer '.$token]);

                return true;
            });
    }
}
