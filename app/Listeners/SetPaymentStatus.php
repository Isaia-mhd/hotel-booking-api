<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Models\Book;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetPaymentStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentCompleted $event): void
    {

        $data = $event->paymentData;

        // Save the payment
        Payment::create([
            "book_id" => $data["book_id"],
            "totalPaid" => $data["amount"]
        ]);


    }
}
