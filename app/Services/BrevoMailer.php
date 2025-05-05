<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailer
{
    public function sendMail($to, $subject, $htmlContent)
    {
        
        $apiKey= config('services.brevo.key');
        $response = Http::withHeaders([
            "api-key" => $apiKey,
            "Content-Type" => "application/json"
        ])->post("https://api.brevo.com/v3/smtp/email", [
            "sender" => [
                "name" => "Mayana Hotel",
                "email" => "mohamedesaie21@gmail.com"
            ],
            "to" => [
                ["email" => $to]
            ],
            "subject" => $subject,
            "htmlContent" => $htmlContent
        ]);

        if (!$response->successful()) {
            // Pour le debug
            logger()->error('Brevo email failed', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
;    }
}

