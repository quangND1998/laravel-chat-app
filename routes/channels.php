<?php

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('room.{id}', function ($user, $id) {
    return new UserResource($user); 
});
Broadcast::channel('sovled-conversation', function ($user, $id) {
    return true;
});
