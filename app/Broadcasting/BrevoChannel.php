<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Services\BrevoMailer;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Notification;

class BrevoChannel
{


    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toBrevo')) {
            return;
        }

        $message = $notification->toBrevo($notifiable);
        $email = $notifiable->routeNotificationFor('mail');


        $htmlContent = view("Mail.forgotPassword", [
            'resetUrl' => $message['resetUrl'],
        ])->render();

        app(BrevoMailer::class)->sendMail(
            $email,
            "Reset Your Password",
            $htmlContent
        );
    }
}
