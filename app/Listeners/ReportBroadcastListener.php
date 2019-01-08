<?php

namespace App\Listeners;

use App\Events\ReportBroadcastEvent;
use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Contracts\Queue\ShouldQueue;

class ReportBroadcastListener
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
     * @param  ReportBroadcastEvent  $event
     * @return void
     */
    public function handle(ReportBroadcastEvent $event)
    {
        //
    }
}
