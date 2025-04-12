<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "users" => User::all()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                "name" => "required|string",
                "email" => "required|email|unique:users",
                "phone" => "required|string",
                "password" => "required|confirmed"
            ]
        );

        $user = User::create($validated);

        return response()->json([
            "message" => "User Stored Successfully!",
            "user" => $user
        ], 200);


    }

    /**
     * Display the specified resource.
     */
    public function show($user)
    {

        $user = User::find($user);

        if (!$user) {
            return response()->json([
                'message' => "User not found",
            ], 404);
        }

        return response()->json([
            'user' => $user,
        ]);



    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate(
            [
                "name" => "required|string",
                "email" => "required|email|exists:users",
                "phone" => "required|string",
            ]
        );

        $user->update($validated);

        return response()->json([
            "message" => "User updated Successfully!",
            "user" => $user
        ], 200);


    }


    public function updateRole(User $user)
    {
        if(!request()->has("role"))
        {
            return response()->json([
                "message" => "role can't be updated",
            ], 422);
        }

        $user->update(["role" => request()->get("role")]);
        return response()->json([
            "message" => "role updated successfully!",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user)
    {

        $user = User::find($user);
        if (!$user) {
            return response()->json([
                'message' => "User does not exist",
            ], 404);
        }

        $user->delete();
        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }
}
