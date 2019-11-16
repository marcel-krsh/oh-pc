<?php

namespace App\Events;

use App\Models\Address;
use App\Models\User;
use Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditorAddressEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $address_id;

    public $address;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $address_id, $address)
    {
        $this->user = $user;
        $this->address_id = $address_id;
        $this->address = $address;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('chat');

        $uid = $this->user->id;
        $sid = $this->user->socket_id;

        return new Channel('auditors.'.$uid.'.'.$sid);
    }
}
