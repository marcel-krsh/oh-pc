<?php

namespace App\Listeners;

use App\Events\MessageSent;

//use Illuminate\Contracts\Queue\ShouldQueue;

class SendChatMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        //
    }
}
