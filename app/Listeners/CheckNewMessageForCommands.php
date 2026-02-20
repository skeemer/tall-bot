<?php

namespace App\Listeners;

use App\Events\NewChatMessage;
use App\Models\Command;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckNewMessageForCommands implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewChatMessage $event): void
    {
        Command::whereEnabled(true)->each(function (Command $command) use ($event) {
            if (preg_match("/^$command->pattern(\\s|\\z)(.*)/", $event->event['message']['text'], $matches)) {
                info('Command matched: '.$command->name);
            }
        });
    }
}
