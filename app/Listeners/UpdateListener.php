<?php

namespace App\Listeners;

use App\Events\UpdateEvent;
use Illuminate\Queue\InteractsWithQueue;

//use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateListener
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
     * @param  CommunicationBroadcastEvent  $event
     * @return void
     */
    public function handle(UpdateEvent $event)
    {
        //
    }
}
