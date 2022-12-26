<?php

namespace App\Events;

use App\Models\Messages;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Models\User
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $room
     * @param  string  $message
     * @return void
     */
    public function __construct(Messages $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if (strpos($this->message->room, '__') !== false) { // must use !== false
            return new PrivateChannel('room.'.$this->message->receiver);
        } else {
            return new PresenceChannel('room.'.$this->message->room);
        }
    }

    // public function broadcastWith()
    // {
    //     return [
    //         'message' => $this->message,
    //         'user' => [
    //             'id' => $this->message->user->id,
    //             'name' => $this->message->user->name,
    //         ],
    //         'created_at' => date('d-m-Y H:i:s', strtotime($this->message->created_at))
    //     ];
    // }
}
