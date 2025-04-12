<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Authentication
Route::get("/reset-password", [AuthController::class, "resetPassword"])->middleware("guest");
Route::get("/forgot-password", [AuthController::class, "forgotPassword"])->middleware("guest");
Route::post("/logout", [AuthController::class, "logout"])->middleware("auth:sanctum");
Route::get("/login", [AuthController::class, "login"])->middleware("guest");



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/users", [UserController::class, "index"]);

Route::post("/users/store", [UserController::class, "store"]);

Route::get("/users/show/{user}", [UserController::class, "show"]);

Route::put("/users/update/{user}", [UserController::class, "update"]);

Route::put("/users/update-role/{user}", [UserController::class, "updateRole"]);

Route::delete("/users/destroy/{user}", [UserController::class, "destroy"]);

