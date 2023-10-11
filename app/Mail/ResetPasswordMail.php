<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;



    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Reset Your Password')
            ->view('emails.reset-password', [
                'token' => $this->token,
                'reset_url' => env('FRONTEND_URL') . '/password/reset/' . $this->token,
            ]);
    }



}
