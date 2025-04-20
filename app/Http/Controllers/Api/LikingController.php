<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LikingController extends Controller
{
    public function liking($room)
    {
        // get user authenticated
        $user = auth()->user();

        // check if the room was already liked by the user authenticated
        $liked = $user->likedRoom()->where('room_id', $room)->exists();


        if ($liked) {
            $user->likedRoom()->detach($room);

            return response()->json([
                'message' => 'Room unliked successfully!',
                'liked' => false
            ]);
        } else {
            $user->likedRoom()->attach($room);

            return response()->json([
                'message' => 'Room liked successfully!',
                'liked' => true
            ]);
        }
    }

}
