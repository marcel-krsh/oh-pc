<?php

namespace App\Listeners;

use App\Events\AuditBroadcast;
use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Contracts\Queue\ShouldQueue;

class AuditBroadcastListener
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
     * @param  AuditBroadcast  $event
     * @return void
     */
    public function handle(AuditBroadcast $event)
    {
        //
    }
}
