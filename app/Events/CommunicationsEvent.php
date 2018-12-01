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
           //Auth::onceUsingId(286); // TEST 
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
        // wrong event to track, we need to detect when recipients are added instead
        //
        //
        // total of unread message for the current user
        // $communication_recipients = CommunicationRecipient::where('communication_id', $communication->id)->with('user')->get()->pluck('user.id','user.socket_id');

        // $data = [
        //     'event' => 'NewMessage',
        //     'users' => [$communication->owner_id],
        //     'data' => [
        //         'stats_communication_total' => $stats_communication_total
        //     ]
        // ];

        // Redis::publish('communications', json_encode($data)); 
    }

    public function communicationRecipientCreated(CommunicationRecipient $communication_recipient)
    { 
        $id = $communication_recipient->id;
        $communication_recipient = CommunicationRecipient::where('id', '=', $id)
                ->with('user')
                ->first();

        $stat = CommunicationRecipient::where('user_id', '=', $communication_recipient->user->id)
                ->where('seen', '=', 0)
                ->count();

        $data = [
            'event' => 'NewRecipient',
            'data' => [
                'userId' => $communication_recipient->user->id,
                'socketId' => $communication_recipient->user->socket_id,
                'stat' => $stat
            ]
        ];

        Redis::publish('communications', json_encode($data)); 
    }
}
