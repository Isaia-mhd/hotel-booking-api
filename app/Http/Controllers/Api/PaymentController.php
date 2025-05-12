<?php

namespace App\Http\Controllers\Api;

use App\Events\PaymentCompleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Book;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{


    private function nightNumber($start_date, $end_date): int
    {

        // Count the night
        $checkIn = Carbon::parse($start_date);
        $checkOut = Carbon::parse($end_date);

        return $checkIn->diffInDays($checkOut);
    }

    public function payment(PaymentRequest $request, Book $book)
    {
        //    try{
        //     Stripe::setApiKey(config('services.stripe.secret'));


        //     $nights = $this->nightNumber($book->start_date, $book->end_date);
        //     $session = Session::create([
        //         'payment_method_types' => ['card'],
        //         'line_items' => [[
        //             'price_data' => [
        //                 'currency' => 'usd',
        //                 'product_data' => [
        //                     'name' => $book->room->name
        //                 ],
        //                 'unit_amount' => $book->room->price * 100, // Amount in cents (... USD)
        //             ],
        //             'quantity' => $nights,
        //         ]],
        //         'mode' => 'payment',
        //         'success_url' => route('success', ["book" => $book->id]),
        //         'cancel_url' => route('cancel'),
        //     ]);

        //     return response()->json([
        //         'url' => $session->url
        //     ], 200);

        //    } catch (\Stripe\Exception\ApiConnectionException $e) {

        //     // Network error (e.g., no internet or Stripe service is down)
        //     return response()->json([
        //         "message" => "Could not connect to Stripe. Please check your internet connection."
        //     ], 500);

        //     } catch (\Stripe\Exception\ApiErrorException $e) {

        //         // General Stripe API error
        //         return response()->json([
        //             "message" => "Payment error: " . $e->getMessage()
        //         ], 402);

        //     } catch (\Stripe\Exception\CardException $e) {
        //         return response()->json([
        //             "message" => "Card payment failed: " . $e->getMessage()
        //         ], 402);
        //     } catch (\Exception $e) {

        //         // Other errors (server issues, etc.)
        //         return response()->json([
        //             "message" => "An unexpected error occurred: " . $e->getMessage()
        //         ], 500);
        //     }




        // $request->validate([
        //     'payment_method_id' => 'required|string',
        //     'amount' => 'required|numeric|min:1', // in USD cents
        //     'book_id' => 'required|integer'
        // ]);

        $book = Book::find($request);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create or confirm the PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount, // amount in cents (e.g., 1000 = $10.00)
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            return response()->json([
                'success' => true,
                'paymentIntent' => $paymentIntent,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    public function success($book)
    {
        try {
            $paid = Book::findOrFail($book);
            $paid->update([
                "isPaid" => true
            ]);

            $nights = $this->nightNumber($paid->start_date, $paid->end_date);
            $paymentData = [
                "book_id" => $paid->id,
                "amount" => $paid->room->price * $nights,
            ];

            event(new PaymentCompleted($paymentData));

            return response()->json([
                "message" => "Booking was Paid successfull.",
                "payment" => $paymentData
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Unexpected error " . $th->getMessage()
            ], 500);
        }
    }

    // public function cancel()
    // {
    //     return response()->json(["message" => "Payment Cancelled!"], 200);
    // }
}
