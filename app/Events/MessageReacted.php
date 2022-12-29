<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReacted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   
    /**
     * Create a new event instance.
     *
     * @return void
     */

     public $reaction;
     public $channel;
 
     public function __construct($reaction, $channel)
     {
         $this->reaction = $reaction;
         $this->channel = $channel;
     }
 
     /**
      * Get the channels the event should broadcast on.
      *
      * @return \Illuminate\Broadcasting\Channel|array
      */
     public function broadcastOn()
     {
         if (strpos($this->channel, '__') !== false) { // must use !== false
             return new PrivateChannel('room.'.$this->channel);
         } else {
             return new PresenceChannel('room.'.$this->channel);
         }
     }
}
