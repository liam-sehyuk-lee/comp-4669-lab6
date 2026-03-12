<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messages\SendMessageRequest;
use App\Models\Message;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use phpcent\Client as CentrifugoClient;

class MessageController extends Controller
{
    private $centrifugo;

    public function __construct()
    {
        $this->centrifugo = new CentrifugoClient(env('WS_API_ENDPOINT'), env('WS_API_KEY'));
        $this->centrifugo->setSecret(env('WS_API_SECRET'));
    }

    /**
     * Endpoint: POST /api/send
     *
     * This is the main endpoint that a client (JS web app, Android app, iOS app, etc.) will
     * hit in order to send a message to another user.
     */
    public function send(SendMessageRequest $request, Room $room): JsonResponse
    {
        $message = $room->messages()->create([
            'from_user_id' => $request->user()->id, 
            'body'         => $request->body,    
        ]);

        $message->load('user');

        $this->centrifugo->publish((string)$room->id, [
            'message' => $message
        ]);

        return response()->json($message, 200);
    }

    /**
     * Endpoint: GET /api/messages/
     */
    public function index(Room $room): JsonResponse
    {
        return response()->json([
            'messages' => $room->messages()->with('user')->get()
        ]);
    }
}
