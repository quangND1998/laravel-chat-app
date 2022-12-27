<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return
        [
            'id' => $this->id,
            'content' => $this->email,
            'receiver' => $this->receiver,
            'sender' => $this->sender,
            'room' => $this->room,
            'created_at' => date('d-m-Y H:i:s', strtotime($this->created_at))
        ];
    }
}
