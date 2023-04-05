<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioSmsSender
{

    private $twilio;

    public function __construct()
    {
        $this->twilio = new Client('AC13397242b39456d518428e1a960f9821','045b3c2ae76c56f6d2f3f95e60b14e5a');
    }

    public function sendOTP($to, $otp)
    {

        // append country code to phone number and remove the first zero
        $to = '+234' . substr($to, 1);

        $message = $this->twilio->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => 'Your OTP is ' . $otp
        ]);

        return $message->sid;
    }
}