<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::all();
        $rooms = Room::all();
        $books = Book::all();
        $payments = Payment::all();

        return response()->json([
            "users" => count($users),
            "rooms" => count($rooms),
            "books" => count($books),
            "payments" => count($payments)
        ]);
    }
}
