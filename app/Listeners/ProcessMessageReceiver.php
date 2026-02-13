<?php

namespace App\Listeners;

use Native\Desktop\Events\ChildProcess\MessageReceived;

class ProcessMessageReceiver
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
    public function handle(MessageReceived $event): void
    {
        info('message received', [$event->alias]);
    }
}
