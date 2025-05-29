<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Classe;
use App\Models\Room;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
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

    public function store(StoreRoomRequest $request)
    {
        request()->validate([
            "image_url" => "required|image|mimes:png,jpg,jpeg"
        ]);

        $class = Classe::where("class", "like", "%".$request->class."%")->first();

        if (Gate::denies("store-room")) {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }

        $paths = null;

        if (request()->hasFile("image_url")) {
            $image = request()->file("image_url");
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();

            $bucket = env('SUPABASE_BUCKET'); // ex: "rooms"
            $supabaseUrl = env('SUPABASE_URL');
            $serviceKey = env('SUPABASE_SERVICE_KEY');


            $response = Http::withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => $image->getMimeType(),
            ])->withBody(
                Utils::streamFor(file_get_contents($image))
            )->put("$supabaseUrl/storage/v1/object/$bucket/$fileName");

            if (!$response->successful()) {
                return response()->json([
                    "message" => "Image upload failed",
                    "error" => $response->body()
                ], 500);
            }

            $paths = "$supabaseUrl/storage/v1/object/public/$bucket/$fileName";
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

    public function update(UpdateRoomRequest $request, $room)
    {
        request()->validate([
            "image_url" => "nullable|image|mimes:png,jpg,jpeg"
        ]);

        $room = Room::find($room);

        if (!$room) {
            return response()->json(["message" => "Room Does Not Exist"], 404);
        }

        if (Gate::denies("update-room")) {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        $class = Classe::where("class", "like", "%".$request->class."%")->first();
        if (!$class) {
            return response()->json(["message" => "Class does not exist"], 422);
        }

        $bucket = env('SUPABASE_BUCKET');
        $supabaseUrl = env('SUPABASE_URL');
        $serviceKey = env('SUPABASE_SERVICE_KEY');

        $imageUrl = $room->image_url;

        if (request()->hasFile("image_url")) {

            if ($imageUrl) {
                $filePath = str_replace("$supabaseUrl/storage/v1/object/public/$bucket/", '', $imageUrl);
                Http::withHeaders([
                    'apikey' => $serviceKey,
                    'Authorization' => 'Bearer ' . $serviceKey,
                ])->delete("$supabaseUrl/storage/v1/object/$bucket/$filePath");
            }


            $image = request()->file("image_url");
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $upload = Http::withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => $image->getMimeType(),
            ])->withBody(
                Utils::streamFor(file_get_contents($image))
            )->put("$supabaseUrl/storage/v1/object/$bucket/$fileName");
            if ($upload->successful()) {
                $imageUrl = "$supabaseUrl/storage/v1/object/public/$bucket/$fileName";
            } else {
                return response()->json([
                    "message" => "Image upload failed",
                    "error" => $upload->body()
                ], 500);
            }
        }

        $room->update([
            "name" => $request->name,
            "price" => $request->price,
            "class_id" => $class->id,
            "image_url" => $imageUrl
        ]);

        return response()->json([
            "message" => "Room updated with success",
            "room" => new RoomResource($room)
        ], 200);
    }


    public function destroy($room)
    {
        $room = Room::find($room);

        if (!$room) {
            return response()->json([
                "message" => "Room Does Not Exist"
            ], 404);
        }

        if (Gate::denies("delete-room")) {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }

        $bucket = env('SUPABASE_BUCKET');
        $supabaseUrl = env('SUPABASE_URL');
        $serviceKey = env('SUPABASE_SERVICE_KEY');

        if ($room->image_url) {
            $filePath = str_replace("$supabaseUrl/storage/v1/object/public/$bucket/", '', $room->image_url);

            $response = Http::withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
            ])->delete("$supabaseUrl/storage/v1/object/$bucket/$filePath");

            if (!$response->successful()) {
                return response()->json([
                    "message" => "Room deleted, but image removal failed",
                    "error" => $response->body()
                ], 500);
            }
        }

        $room->delete();

        return response()->json([
            "message" => "Room deleted with success"
        ], 200);
    }

}
