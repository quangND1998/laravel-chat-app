<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Resources\MessageResource;
class MessageController extends Controller
{
    public function index(Request $request){
        $messages = Messages::with(['sender', 'receiver'])->where('room', $request->query('room', ''))->orderBy('created_at', 'asc')->get();

        return response()->json($messages, Response::HTTP_OK);
   

    }


    public function store(Request $request)
    {
        $message = new Messages();
        $message->sender = Auth::user()->id;
        $message->content = $request->input('content', '');

        if ($request->has('receiver') && $request->input('receiver')) {
            $receiver = (int) $request->input('receiver');
            $message->receiver = $receiver;
            $message->room = $message->sender < $receiver ? $message->sender . '__' . $receiver : $receiver . '__' . $message->sender;
        } else {
            $message->room = $request->input('room');
        }

        $message->save();

        broadcast(new Message($message->load('sender')))->toOthers(); // send to others EXCEPT user who sent this message

        return response()->json(['message' => $message->load('sender')]);
    }
}
