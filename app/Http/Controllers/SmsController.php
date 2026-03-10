<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // Send Single SMS
    public function send($number, $message)
    {
        $response = $this->smsService->sendSms($number, $message);

        return response()->json($response);
    }


    // Send Bulk SMS
    public function sendBulk(Request $request)
    {

        $numbers = $request->numbers;
        $message = $request->message;
        $campaign = $request->campaignName;

        $response = $this->smsService->sendBulkSms($numbers, $message, $campaign);

        return response()->json($response);
    }

    public function sendOtp($number)
    {
        $response = $this->smsService->sendOtp($number);

        return response()->json($response);
    }


    // Check Balance
    public function balance()
    {
        $response = $this->smsService->getBalance();

        return response()->json($response);
    }
}
