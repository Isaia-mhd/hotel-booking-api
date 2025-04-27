<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(["classes" => Classe::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "class" => "required|string|unique:classes,class"
        ]);

        if(Gate::denies("add-class"))
        {
            return response()->json(["message"=> "Unauthorized"],403);
        }

        $classe = Classe::create([
            "class" => $request->class
        ]);

        return response()->json([
            "message" => "New Classe Created successfully.",
            "class" => $classe
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classe $class)
    {

        $request->validate([
            "class" => "required|string|exists:classes,class"
        ]);

        if(Gate::denies("add-class"))
        {
            return response()->json(["message"=> "Unauthorized"],403);
        }

        $class->update($request->class);

        return response()->json([
            "message" => "New Classe Created successfully.",
            "class" => $class
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($class)
    {
        $classe = Classe::find($class);
        $rooms = Room::where("class_id", $class)->get();

        if(!$class)
        {
            return response()->json([
                "message" => "Class does not exist."
            ], 404);
        }

        if($rooms)
        {
            return response()->json([
                "message" => "Some room is related to this class."
            ], 409);
        }

        $classe->delete();

        return response()->json([
            "message" => "Class deleted successfully."
        ], 200);

    }
}
