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

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreRoomRequest $request)
    // {

    //     request()->validate([
    //         "image_url" => "required|image|mimes:png,jpg,jpeg"
    //     ]);

    //     $class= Classe::where("class", "like", "%".$request->class ."%")->first();


    //     if(Gate::denies("store-room"))
    //     {
    //         return response()->json([
    //             "message" => "Unauthorized"
    //         ], 403);
    //     }

    //     $paths = null;
    //     if(request()->hasFile("image_url"))
    //     {
    //         $image = request()->file("image_url");

    //         $path = $image->store("/rooms", "public");
    //         $paths = 'storage/'. $path;
    //     }



    //     $room = Room::create([
    //         "name" => $request->name,
    //         "class_id" => $class->id,
    //         "price" => $request->price,
    //         "image_url" => $paths
    //     ]);

    //     return response()->json([
    //         "message" => "Room created with success",
    //         "room" => new RoomResource($room)
    //     ], 201);
    // }


    public function store(StoreRoomRequest $request)
    {
        request()->validate([
            "image_url" => "required|image|mimes:png,jpg,jpeg"
        ]);

        $class = Classe::where("class", "like", "%".$request->class ."%")->first();

        if (Gate::denies("store-room")) {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }

        $imageUrl = null;

        if (request()->hasFile("image_url")) {
            $image = request()->file("image_url");
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $bucket = env('SUPABASE_BUCKET');

            // Upload vers Supabase Storage
            $response = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => $image->getMimeType(),
            ])->put(env('SUPABASE_URL') . "/storage/v1/object/$bucket/$fileName", file_get_contents($image));

            if ($response->successful()) {
                // Génère l'URL publique
                $imageUrl = env('SUPABASE_URL') . "/storage/v1/object/public/$bucket/$fileName";
            } else {
                return response()->json([
                    "message" => "Image upload failed",
                    "error" => $response->body()
                ], 500);
            }
        }

        $room = Room::create([
            "name" => $request->name,
            "class_id" => $class->id,
            "price" => $request->price,
            "image_url" => $imageUrl
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
    // public function update(UpdateRoomRequest $request, $room)
    // {
    //     request()->validate([
    //         "image_url" => "image|mimes:png,jpg,jpeg"
    //     ]);

    //     $room = Room::find($room);

    //     if(!$room)
    //     {
    //         return response()->json([
    //             "message" => "Room Does Not Exist"
    //         ],  404);
    //     }

    //     if(Gate::denies("update-room"))
    //     {
    //         return response()->json([
    //             "message" => "Unauthorized"
    //         ], 403);
    //     }


    //     $class= Classe::where("class", "like", "%".$request->class ."%")->first();
    //     if(!$class)
    //     {
    //         return response()->json(["message" => "Class does not exist"]);
    //     }

    //     $paths = null;
    //     if(request()->hasFile("image_url"))
    //     {
    //         $image = request()->file("image_url");

    //         $path = $image->store("/rooms", "public");
    //         $paths = 'storage/'. $path;
    //     }

    //     $room->update([
    //         "name" => $request->name,
    //         "price" => $request->price,
    //         "class_id" => $class->id,
    //         "image_url" => $paths
    //     ]);

    //     return response()->json([
    //         "message" => "Room updated with success",
    //         "room" => new RoomResource($room)
    //     ], 200);
    // }

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
            return response()->json(["message" => "Class does not exist"]);
        }

        $bucket = env('SUPABASE_BUCKET');
        $supabaseUrl = env('SUPABASE_URL');
        $serviceKey = env('SUPABASE_SERVICE_KEY');

        $imageUrl = $room->image_url; // valeur actuelle

        if (request()->hasFile("image_url")) {
            // Supprimer l’ancienne image si elle existe
            if ($imageUrl) {
                $filePath = str_replace("$supabaseUrl/storage/v1/object/public/$bucket/", '', $imageUrl);
                Http::withHeaders([
                    'apikey' => $serviceKey,
                    'Authorization' => 'Bearer ' . $serviceKey,
                ])->delete("$supabaseUrl/storage/v1/object/$bucket/$filePath");
            }

            // Upload de la nouvelle image
            $image = request()->file("image_url");
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $upload = Http::withHeaders([
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => $image->getMimeType(),
            ])->put("$supabaseUrl/storage/v1/object/$bucket/$fileName", file_get_contents($image));

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


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($room)
    // {

    //     $room = Room::find($room);

    //     if(!$room)
    //     {
    //         return response()->json([
    //             "message" => "Room Does Not Exist"
    //         ], 404);
    //     }

    //     if(Gate::denies("delete-room"))
    //     {
    //         return response()->json([
    //             "message" => "Unauthorized"
    //         ], 403);
    //     }

    //     // Check if the room has image
    //     if($room->image_url)
    //     {
    //         $imagePath = str_replace('storage/', 'public/', $room->image_url);
    //         if(Storage::exists($imagePath))
    //         {
    //             Storage::delete($imagePath);
    //         }
    //     }


    //     $room->delete();
    //     return response()->json([
    //         "message" => "Room deleted with success"
    //     ], 200);
    // }


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

        // Supprimer l’image depuis Supabase Storage
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
