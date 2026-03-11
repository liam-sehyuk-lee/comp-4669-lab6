<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messages\SendMessageRequest;
use App\Models\Message;

use App\Models\Room;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function __construct()
    {
        // TODO: Create a CentrifugoClient instance and set the API key and secret
    }

    /**
     * Endpoint: POST /api/send
     *
     * This is the main endpoint that a client (JS web app, Android app, iOS app, etc.) will
     * hit in order to send a message to another user.
     */
    public function send(SendMessageRequest $request, Room $room): JsonResponse
    {
        // TODO: Create a new Message instance, set its properties based on the request data, and save it to the database
        // After saving the message, use the CentrifugoClient to broadcast the new message to all clients subscribed to the room's channel
        return response()->json();
    }

    /**
     * Endpoint: GET /api/messages/
     */
    public function index(Room $room): JsonResponse
    {
        // TODO: Retrieve all messages for the specified room and return them as a JSON response
        // Note that the client-side expects the messages to be returned in a JSON object with the key "messages"
        return response()->json();
    }
}
