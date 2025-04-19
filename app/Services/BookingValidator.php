<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Carbon;

class BookingValidator
{
    public static function validateStoring($roomId, $startDate, $endDate)
    {
        // Check if the start date is > end date
        if ($startDate > $endDate) {
            return response()->json([
                "message" => "The end-date must be after the start-date."
            ], 422);
        }

        // Check if the start date has been passed
        if ($startDate <= now()) {
            return response()->json([
                "message" => "The start_date has been passed!"
            ], 409);
        }

        // Check if the room is already booked
        $alreadyBooked = Book::where('room_id', $roomId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start-date', [$startDate, $endDate])
                      ->orWhereBetween('end-date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start-date', '<=', $startDate)
                            ->where('end-date', '>=', $endDate);
                      });
            })
            ->exists();

        if ($alreadyBooked) {
            return response()->json([
                "message" => "This room is occupied for this date."
            ], 409);
        }

        // If all is good, return null
        return null;
    }

    public static function validateUpdating($bookId, $startDate, $endDate)
    {

        $book = Book::find($bookId);

        if(!$book)
        {
           return response()->json([
               "message" => "Booking does not exist"
           ], 404);
        }

        // Check if the start date is > end date
        if ($startDate > $endDate) {
            return response()->json([
                "message" => "The end-date must be after the start-date."
            ], 422);
        }

        // Check if the start date has been passed
        if ($startDate <= now()) {
            return response()->json([
                "message" => "The start_date has been passed!"
            ], 409);
        }
        
        return null;
    }

}
