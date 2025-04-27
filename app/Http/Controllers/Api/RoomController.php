<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Classe;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with("classe")->paginate(4);

        return response()->json(["rooms" => $rooms], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $class= Classe::where("class", "like", "%".$request->class ."%")->first();


        if(Gate::denies("store-room"))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }

        $room = Room::create([
            "name" => $request->name,
            "class_id" => $class->id,
            "price" => $request->price
        ]);

        return response()->json([
            "message" => "Room created with success",
            "room" => $room
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($room)
    {

        $room = Room::with("classe")->find($room);

        if(!$room)
        {
            return response()->json([
                "message" => "Room Does Not Exist"
            ],  404);
        }

        return response()->json([
            "room" => $room,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, $room)
    {

        $room = Room::find($room);

        if(!$room)
        {
            return response()->json([
                "message" => "Room Does Not Exist"
            ],  404);
        }

        if(Gate::denies("update-room"))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }


        $class= Classe::where("class", "like", "%".$request->class ."%")->first();
        if(!$class)
        {
            return response()->json(["message" => "Class does not exist"]);
        }

        $room->update([
            "name" => $request->name,
            "price" => $request->price,
            "class_id" => $class->id
        ]);

        return response()->json([
            "message" => "Room updated with success",
            "room" => $room
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($room)
    {

        $room = Room::find($room);

        if(!$room)
        {
            return response()->json([
                "message" => "Room Does Not Exist"
            ], 404);
        }

        if(Gate::denies("delete-room"))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }



        $room->delete();
        return response()->json([
            "message" => "Room deleted with success"
        ], 200);
    }
}
