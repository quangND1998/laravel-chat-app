<?php

namespace App\Http\Controllers;

use App\Events\MessageReacted;
use App\Models\Messages;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function react(Request $request) {
        $message = Messages::with(['sender', 'receiver', 'reactions.user','room'])->findOrFail($request->input('msg_id'));
        $reaction = Reaction::where(['msg_id' => $request->input('msg_id'), 'user_id' => $request->input('user_id')])->first();
       
        
        if ($reaction) {
           
            if ($reaction->emoji_id == $request->input('emoji_id')) {
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
        $message->load(['reactions.user','room']);
        broadcast(new MessageReacted($message, $message->room))->toOthers();

        return ['success' => true, 'message' => $message];
    }
}
