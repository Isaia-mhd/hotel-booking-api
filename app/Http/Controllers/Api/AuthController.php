<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\forgotPasswordUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\resetPasswordUserRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function login(LoginUserRequest $request)
    {

        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "The provided password is incorrect"
            ], 422);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response()->json([
            "message" => "User Logged In Succesfully!",
            "user" => $user,
            "token" => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out',
        ], 200);
    }

    public function forgotPassword(forgotPasswordUserRequest $request)
    {

        $user = User::where("email", $request->email)->first();

        if (!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $token = Password::createToken($user);
        $user->notify(new ResetPasswordNotification($token, $user->email));

        return response()->json([
            "message" => "Password Reset Link Sent.",
            "email" => $request->email,
            "token" => $token
        ]);

    }

    public function resetPassword(resetPasswordUserRequest $request)
    {

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'], 200)
            : response()->json(['message' => 'Password reset failed.'], 500);

    }
}

