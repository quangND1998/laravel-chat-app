<?php

namespace App\Http\Controllers;

use App\Events\MessageReacted;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function react(Request $request) {
        $reaction = Reaction::where(['msg_id' => $request->input('msg_id'), 'user_id' => $request->input('user_id')])->first();
       

        if ($reaction) {
            if ($reaction->emoji_id === $request->input('emoji_id')) {
                $reaction->delete();
            } else {
                $reaction->emoji_id = $request->input('emoji_id');

                $reaction->save();
            }
        } else {
            $reaction = new Reaction();
            $reaction->msg_id = $request->input('msg_id');
            $reaction->user_id = $request->input('user_id');
            $reaction->emoji_id = $request->input('emoji_id');

            $reaction->save();
        }
        $reaction->load(['user','message.room']);
        broadcast(new MessageReacted($reaction->user, $reaction->message->room))->toOthers();

        return ['success' => true, 'reaction' => $reaction];
    }
}
