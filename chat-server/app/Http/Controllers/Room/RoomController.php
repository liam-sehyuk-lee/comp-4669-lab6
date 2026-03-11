<?php

namespace App\Http\Controllers\Room;

use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\GetRoomsRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoomController {

    /**
     * @param GetRoomsRequest $request
     * @return JsonResponse
     */
    public function index(GetRoomsRequest $request): JsonResponse {
        return response()->json(['rooms' => Room::all()]);
    }

    /**
     * @param CreateRoomRequest $request
     * @return JsonResponse
     */
    public function store(CreateRoomRequest $request): JsonResponse {

        $reqData = $request->validated();
        $name = $reqData['name'];

        $room = new Room();

        $room->name = $name;
        $room->save();

        return response()->json($room, Response::HTTP_OK);
    }

}
