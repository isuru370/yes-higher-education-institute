<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // POST /api/send-sms/single
    public function send(Request $request)
    {
        $validated = $request->validate([
            'number' => ['required', 'string'],
            'message' => ['required', 'string', 'max:612'],
        ]);

        $response = $this->smsService->sendSms(
            $validated['number'],
            $validated['message']
        );

        return response()->json($response);
    }

    // POST /api/send-sms/bulk
    public function sendBulk(Request $request)
    {
        $validated = $request->validate([
            'numbers' => ['required', 'array', 'min:1'],
            'numbers.*' => ['required', 'string'],
            'message' => ['required', 'string', 'max:612'],
            'campaignName' => ['nullable', 'string', 'max:100'],
        ]);

        $response = $this->smsService->sendBulkSms(
            $validated['numbers'],
            $validated['message'],
            $validated['campaignName'] ?? 'LaravelCampaign'
        );

        return response()->json($response);
    }

    // POST /api/send-sms/otp
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'number' => ['required', 'string'],
        ]);

        $response = $this->smsService->sendOtp($validated['number']);

        return response()->json($response);
    }

    // GET /api/send-sms/balance
    public function balance()
    {
        $response = $this->smsService->getBalance();

        return response()->json($response);
    }
}
