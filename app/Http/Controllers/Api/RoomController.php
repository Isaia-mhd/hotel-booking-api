<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $rooms = Room::with("classe")->get();

        return response()->json(["rooms" => $rooms], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "porte" => "string|nullable|unique:rooms",
            "price" => "required|numeric",
            "class" => "required|string"
        ]);

        $class= Classe::where("class", "like", "%".$request->class ."%")->first();

        $room = Room::create([
            "name" => $request->name,
            "porte" => $request->porte,
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
    public function update(Request $request, $room)
    {
        $request->validate([
            "name" => "required|string",
            "porte" => "string|nullable",
            "price" => "required|numeric",
            "class" => "string|required"
        ]);

        $room = Room::find($room);

        if(!$room)
        {
            return response()->json([
                "message" => "Room Does Not Exist"
            ],  404);
        }

        if(Gate::denies("update-room", $room))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }


        $class= Classe::where("class", "like", "%".$request->class ."%")->first();

        $room->update([
            "name" => $request->name,
            "porte" => $request->porte,
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
                "message" => "Romm Does Not Exist"
            ], 404);
        }
        
        if(Gate::denies("delete-room", $room))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }



        $room->delete();
        return response()->json([
            "message" => "Romm deleted with success"
        ], 200);
    }
}
