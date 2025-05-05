<?php

namespace App\Notifications;

use App\Broadcasting\BrevoChannel;
use App\Services\BrevoMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class ResetPasswordNotification extends Notification
{
    use Queueable;



    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }


    public function via(object $notifiable): array
    {
        return [BrevoChannel::class];
    }


    public function toBrevo($notifiable)
    {
        $resetUrl = config("app.front_end_url") . '/reset-password';

        return [
            'subject' => 'Reset Your Password',
            'resetUrl' => $resetUrl,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
