<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Models\Book;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\NewPayment;
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
        // Save the payment
        Payment::create([
            "book_id" => $event->bookId,
            "totalPaid" => ($event->paymentIntent->amount / 100),
        ]);

        //Change the status of book to be PAID
        $book = Book::find($event->bookId);
        $book->update([
            "isPaid" => true
        ]);

        //Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin)
        {
            $admin->notify(new NewPayment($book));
        }


    }
}
