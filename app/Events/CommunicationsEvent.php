<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\Communication;
use App\Models\CommunicationRecipient;
use Illuminate\Support\Facades\Redis;
use Auth;

class CommunicationsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
           Auth::onceUsingId(1); // TEST BRIAN
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function communicationCreated(Communication $communication)
    {
        $stats_communication_total = CommunicationRecipient::where('user_id', $communication->owner_id)
                    ->where('seen', 0)
                    ->count();
        $data = [
            'event' => 'NewMessage',
            'data' => [
                'stats_communication_total' => $stats_communication_total
            ]
        ];

        Redis::publish('communications', json_encode($data)); 
    }
}
