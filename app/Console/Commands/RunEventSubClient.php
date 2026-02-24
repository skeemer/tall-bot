<?php

namespace App\Console\Commands;

use Amp\Websocket\WebsocketMessage;
use App\Actions\GetTwitchRequestClient;
use App\Actions\TwitchConnections\GetBotConnection;
use App\Events\NewChatMessage;
use App\Events\SubscriptionSuccess;
use Illuminate\Console\Command;

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
    public function handle(): void
    {
        $twitchConnection = GetBotConnection::run();

        if (! $twitchConnection) {
            $this->error('Bot connection not found');

            return;
        }

        $connection = connect(config('services.twitch.websocket_url'));

        foreach ($connection as $message) {
            /** @var WebsocketMessage $message */
            if ($message->isText()) {
                $parsed = json_decode($message->read(), true);

                if ($parsed['metadata']['message_type'] === 'session_welcome') {
                    $this->info('Session welcome received');

                    sleep(1); // To make it easier to cancel the connection

                    // Setup subscriptions
                    $data = [
                        'type' => 'channel.chat.message',
                        'version' => '1',
                        'transport' => [
                            'method' => 'websocket',
                            'session_id' => $parsed['payload']['session']['id'],
                        ],
                        'condition' => [
                            'broadcaster_user_id' => $twitchConnection->twitch_user_id,
                            'user_id' => $twitchConnection->twitch_user_id,
                        ],
                        'session_id' => $parsed['payload']['session']['id'],
                    ];
                    $response = GetTwitchRequestClient::run($twitchConnection)
                        ->post('/eventsub/subscriptions', $data);
                    if ($response->successful()) {
                        SubscriptionSuccess::broadcast();
                    }
                } elseif ($parsed['metadata']['message_type'] === 'session_keepalive') {
                    // Do nothing
                    continue;
                } elseif ($parsed['metadata']['message_type'] === 'notification') {
                    if ($parsed['payload']['subscription']['type'] === 'channel.chat.message') {
                        $event = $parsed['payload']['event'];
                        $event['created_at'] = now();
                        NewChatMessage::dispatch($event);
                    } else {
                        $this->info('Notification received: '.$parsed['payload']['subscription']['type']);
                    }
                } else {
                    $this->info('Message type: '.$parsed['metadata']['message_type']);
                }
            } else {
                $this->info($message->read());
            }
        }
    }
}
