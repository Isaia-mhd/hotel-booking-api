<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Room;
use App\Services\BookingValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Book::with("user", "room.classe")->get();
        return response()->json([
            "bookings" => $bookings
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request, $room)
    {

        $nbPeople = $request->number_of_people;
        $startDate = $request->start_date;
        $endDate = $request->end_date;


        // Validate the date
        $bookingValidationResponse = BookingValidator::validateStoring($room, $startDate, $endDate);

        if($bookingValidationResponse)
        {
            return $bookingValidationResponse;
        }

        $booking = Book::create([
            'room_id' => $room,
            'user_id' => auth()->id(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'number_of_people' => $nbPeople
        ]);

        return response()->json([
            "message" => "Booking Placed successfully",
            "book" => $booking
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($book)
    {
        $book = Book::find($book);
        if(!$book)
        {
            return response()->json([
                "message" => "Booking does not exist",
            ], 404);
        }

        return response()->json([
            "book" => $book
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, $book)
    {

        $nbPeople = $request->number_of_people;
        $startDate = $request->start_date;
        $endDate = $request->end_date;


        // Validate the date
        $bookingValidationResponse = BookingValidator::validateUpdating($book, $startDate, $endDate);

        if($bookingValidationResponse)
        {
            return $bookingValidationResponse;
        }
        $book = Book::find($book);
        $book->update([
            "start_date" => $startDate,
            "end_date" => $endDate,
            "number_of_people" => $nbPeople
        ]);

        return response()->json([
            "message" => "Booking updated with success!",
            "book" => $book
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($book)
    {

        $book = Book::find($book);
        if(!$book)
        {
           return response()->json([
               "message" => "Booking does not exist"
           ], 404);
        }

        if(Gate::denies("delete-book"))
        {
            return response()->json([
                "message" => "The only Admin can delete this booking."
            ], 403);
        }

        $book->delete();

        return response()->json([
            "message" => "Booking deleted with success",
        ], 200);
    }

    public function cancel($book)
    {

        $book = Book::find($book);

        if(!$book)
        {
            return response()->json([
                "message" => "Booking does not exist"
            ], 404);
        }


        $book->update(["isCanceled" => true]);
        return response()->json([
            "message" => "Booking Canceled"
        ], 200);
    }
}
