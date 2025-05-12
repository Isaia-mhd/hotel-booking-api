<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Classe;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with("classe")->get();

        return response()->json([
            "rooms" => RoomResource::collection($rooms)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {

        request()->validate([
            "image_url" => "required|image|mimes:png,jpg,jpeg"
        ]);

        $class= Classe::where("class", "like", "%".$request->class ."%")->first();


        if(Gate::denies("store-room"))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }

        $paths = null;
        if(request()->hasFile("image_url"))
        {
            $image = request()->file("image_url");

            $path = $image->store("/rooms", "public");
            $paths = 'storage/'. $path;
        }



        $room = Room::create([
            "name" => $request->name,
            "class_id" => $class->id,
            "price" => $request->price,
            "image_url" => $paths
        ]);

        return response()->json([
            "message" => "Room created with success",
            "room" => new RoomResource($room)
        ], 201);
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
            "room" => new RoomResource($room),
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
            "room" => new RoomResource($room)
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

        // Check if the room has image
        if($room->image_url)
        {
            $imagePath = str_replace('storage/', 'public/', $room->image_url);
            if(Storage::exists($imagePath))
            {
                Storage::delete($imagePath);
            }
        }


        $room->delete();
        return response()->json([
            "message" => "Room deleted with success"
        ], 200);
    }
}
