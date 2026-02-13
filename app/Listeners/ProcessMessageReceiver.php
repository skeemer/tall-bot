<?php

namespace App\Listeners;

use Native\Desktop\Events\ChildProcess\MessageReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
