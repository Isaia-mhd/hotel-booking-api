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

    public function payment(PaymentRequest $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create or confirm the PaymentIntent
            $amount = floatval($request->amount);
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($amount * 100), // amount in cents (e.g., 1000 = $10.00)
                'currency' => 'usd',
                'payment_method' => $request->payment_method,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            event(new PaymentCompleted($paymentIntent, $request->book_id));

            return response()->json([
                'message' => 'Payment Successfull.',
                'paymentData' => $paymentIntent,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment Failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
