<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{

    // Single SMS
    public function sendSms($recipient, $message)
    {
        $recipient = $this->formatNumber($recipient);

        $response = Http::get('https://smsapi.chatbiz.net/v1/send/', [
            'user_id' => env('SMS_USER_ID'),
            'api_key' => env('SMS_API_KEY'),
            'sender_id' => env('SMS_SENDER_ID'),
            'message' => $message,
            'recipient_contact_no' => $recipient
        ]);

        return $response->json();
    }


    // Bulk SMS
    public function sendBulkSms($numbers, $message, $campaign = "LaravelCampaign")
    {

        $formattedNumbers = [];

        foreach ($numbers as $number) {
            $formattedNumbers[] = $this->formatNumber($number);
        }

        $response = Http::asForm()->post('https://smsapi.chatbiz.net/v1/bulk/', [
            'user_id' => env('SMS_USER_ID'),
            'api_key' => env('SMS_API_KEY'),
            'sender_id' => env('SMS_SENDER_ID'),
            'campaign_name' => $campaign,
            'message' => $message,
            'recipient_contact_no' => implode(',', $formattedNumbers)
        ]);

        return $response->json();
    }


    // Send OTP SMS
    public function sendOtp($number)
    {
        $otp = $this->generateOtp();

        $message = "Your verification code is: {$otp}";

        $response = $this->sendSms($number, $message);

        return [
            'otp' => $otp,
            'sms_response' => $response
        ];
    }


    // Generate 6 digit OTP
    private function generateOtp()
    {
        return rand(100000, 999999);
    }


    // Get SMS Balance
    public function getBalance()
    {
        $response = Http::get('https://smsapi.chatbiz.net/v1/getBalance/', [
            'user_id' => env('SMS_USER_ID'),
            'api_key' => env('SMS_API_KEY')
        ]);

        return $response->json();
    }


    // Format Sri Lanka numbers
    private function formatNumber($number)
    {
        if (str_starts_with($number, '0')) {
            return '94' . substr($number, 1);
        }

        if (str_starts_with($number, '+94')) {
            return substr($number, 1);
        }

        return $number;
    }
}