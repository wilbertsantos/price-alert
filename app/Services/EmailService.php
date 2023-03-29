<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send an email alert.
     *
     * @param string $recipient
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function sendAlert($message)
    {
        $recipient = env('ALERT_EMAIL_RECIPIENT');
        $subject = env('ALERT_EMAIL_SUBJECT');

        Mail::raw($message, function($email) use ($recipient, $subject) {
            $email->to($recipient)->subject($subject);
        });
    }

}
