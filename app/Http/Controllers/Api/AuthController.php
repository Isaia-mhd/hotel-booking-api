<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\forgotPasswordUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\resetPasswordUserRequest;
use App\Models\User;
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


        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent to your email.'], 200)
            : response()->json(['message' => 'Failed to send reset link.'], 500);



    }

    public function resetPassword(resetPasswordUserRequest $request)
    {

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'], 200)
            : response()->json(['message' => 'Password reset failed.'], 500);

    }
}

