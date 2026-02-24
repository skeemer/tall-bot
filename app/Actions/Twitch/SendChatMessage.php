<?php

namespace App\Actions\Twitch;

use App\Actions\GetTwitchRequestClient;
use App\Actions\TwitchConnections\GetBotConnection;
use App\Actions\TwitchConnections\GetChatConnection;
use Lorisleiva\Actions\Concerns\AsAction;

class SendChatMessage
{
    use AsAction;

    public function handle(string $message, bool $isChatter = false): array
    {
        $botConnection = GetBotConnection::run();
        $chatConnection = $isChatter ? GetChatConnection::run() : $botConnection;

        return GetTwitchRequestClient::run($chatConnection)->post('/chat/messages', [
            'broadcaster_id' => $botConnection->twitch_user_id,
            'sender_id' => $chatConnection->twitch_user_id,
            'message' => $message,
        ])->json();
    }
}
