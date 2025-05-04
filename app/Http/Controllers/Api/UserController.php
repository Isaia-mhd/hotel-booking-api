<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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

        /**
     * @property string $name
     * @property string $email
     * @property string $password
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            "message" => "User Stored Successfully!",
            "user" => $user,
        ], 201);


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
    public function update(UpdateUserRequest $request, $user)
    {

        $userInfo = User::find($user);

        if(!$userInfo)
        {
            return response()->json([
                "message" => "Room Does Not Exist"
            ],  404);
        }

        if(Gate::denies("update-user", $userInfo))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }

        $userInfo->update($request->all("name","email", "phone"));

        return response()->json([
            "message" => "User updated Successfully!",
            "user" => $userInfo
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


        if(Gate::denies("change-role"))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
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

        $userInfo = User::find($user);
        if (!$userInfo) {
            return response()->json([
                'message' => "User does not exist",
            ], 404);
        }

        if(Gate::denies("delete-user", $userInfo))
        {
            return response()->json([
                "message" => "Unauthorized"
            ], 403);
        }

        $userInfo->delete();
        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }
}
