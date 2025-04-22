<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return ['mail'];
    }


    public function toMail($notifiable)
    {
        $frontendUrl = config("app.front_end_url") . '/reset-password?token=' . $this->token . '&email=' . urlencode($this->email);

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->line('Click the button below to reset your password.')
            ->action('Reset Password', $frontendUrl)
            ->line('If you didn’t request a password reset, no further action is required.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
