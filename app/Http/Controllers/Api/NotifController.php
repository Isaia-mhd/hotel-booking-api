<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotifController extends Controller
{
    public function getNotifications(){
        if(Gate::denies("get-notifications"))
        {
            return response()->json(["message" => "Unauthorized"], 403);
        }
        
        $admin = User::where('id', auth()->id())
        ->where("role", "admin")
                        ->first();

        return $admin->notifications;
    }
}
