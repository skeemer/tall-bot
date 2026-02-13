<?php

namespace App\Console\Commands;

use Amp\Websocket\WebsocketMessage;
use App\Actions\GetTwitchRequestClient;
use App\Actions\RefreshAccessToken;
use App\Models\TwitchConnection;
use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use function Amp\Websocket\Client\connect;

class RunEventSubClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-event-sub-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = connect('wss://eventsub.wss.twitch.tv/ws');

        $twitchConnection = TwitchConnection::where('channel_id', '!=', null)->first();

        foreach ($connection as $message) {
            /** @var WebsocketMessage $message */
            if ($message->isText()) {
                $parsed = json_decode($message->read(), true);

                if ($parsed['metadata']['message_type'] === 'session_welcome') {
                    $this->info('Session welcome received');
                    $data = [
                        'type' => 'channel.chat.message',
                        'version' => '1',
                        'transport' => [
                            'method' => 'websocket',
                            'session_id' => $parsed['payload']['session']['id'],
                        ],
                        'condition' => [
                            'broadcaster_user_id' => $twitchConnection->channel,
                            'user_id' => $twitchConnection->channel,
                        ],
                        'session_id' => $parsed['payload']['session']['id'],
                    ];
                    GetTwitchRequestClient::run($twitchConnection)
                        ->post('https://api.twitch.tv/helix/eventsub/subscriptions', $data);

                } else {
                    $this->info('Message type: '.$parsed['metadata']['message_type']);
                }
            } else {
                $this->info($message->read());
            }
        }
    }
}
