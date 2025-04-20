<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\LikingController;
use App\Http\Controllers\Api\NotifController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ContactController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|role--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("/contacts/store", [ContactController::class,"store"]);
Route::get("/contacts", [ContactController::class,"getAll"]);



Route::middleware(['auth:sanctum'])->group(function () {


    // Notification*
    Route::get('/admin/notifications', [NotifController::class, "getNotifications"]);

    // Booking a room
    Route::put("/books/cancel/{book}/", [BookingController::class, "cancel"]);
    Route::delete("/books/delete/{book}/", [BookingController::class, "destroy"]);
    Route::put("/books/update/{book}/", [BookingController::class, "update"]);
    Route::get("/books/show/{book}/", [BookingController::class, "show"]);
    Route::post("/books/store/{room}", [BookingController::class, "store"]);
    Route::get("/books", [BookingController::class, "index"]);



    // Room
    Route::post("/rooms/{room}/liking", [LikingController::class, "liking"]);
    Route::delete("/rooms/delete/{room}", [RoomController::class, "destroy"]);
    Route::put("/rooms/update/{room}", [RoomController::class, "update"]);
    Route::get("/rooms/show/{room}", [RoomController::class, "show"]);
    Route::post("/rooms/store", [RoomController::class, "store"]);
    Route::get("/rooms", [RoomController::class, "index"]);

    // AUTH LOGOUT
    Route::post("/logout", [AuthController::class, "logout"]);

    // USER
    Route::get("/users", [UserController::class, "index"]);
    Route::get("/users/show/{user}", [UserController::class, "show"]);
    Route::put("/users/update/{user}", [UserController::class, "update"]);
    Route::put("/users/update-role/{user}", [UserController::class, "updateRole"]);
    Route::delete("/users/destroy/{user}", [UserController::class, "destroy"]);
});

Route::middleware(['guest'])->group(function () {
    // Authentication
    Route::get("/reset-password", [AuthController::class, "resetPassword"]);
    Route::get("/forgot-password", [AuthController::class, "forgotPassword"]);
    Route::get("/login", [AuthController::class, "login"]);

    Route::post("/users/store", [UserController::class, "store"]);
});






Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


