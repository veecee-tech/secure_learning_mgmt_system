<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioSmsSender
{

    private $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILLIO_ACCOUNT_SID'),env('TWILLIO_AUTH_TOKEN'));
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