<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected string $baseUrl;
    protected string $userId;
    protected string $apiKey;
    protected string $senderId;

    private const STATUS_MESSAGES = [
        201 => 'Sender ID API Status is Inactive',
        202 => 'Invalid API Key',
        203 => 'Contact Number is Invalid or Operator not Support',
        204 => 'Successfully Sent the Message',
        205 => 'Length of Contact is Invalid',
        206 => 'Country is not Available for Sending SMS',
        207 => 'No Contact Numbers',
        208 => 'Account Balance is Insufficient',
        209 => 'Sending the Message is Unsuccessful',
        210 => 'Client is Suspended',
        211 => 'No Sender ID / Sender ID is not Approved',
        212 => 'Rate Card is not Set for the Country',
        213 => 'Route is not Set for the Country',
        214 => 'API Maintenance',
    ];

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.sms.base_url') ?? 'https://smsapi.chatbiz.net/v1', '/');
        $this->userId = (string) config('services.sms.user_id');
        $this->apiKey = (string) config('services.sms.api_key');
        $this->senderId = (string) config('services.sms.sender_id');
    }

    // Single SMS
    public function sendSms(string $recipient, string $message): array
    {
        try {
            $recipient = $this->formatNumber($recipient);

            $response = Http::timeout(15)->get("{$this->baseUrl}/send", [
                'user_id' => $this->userId,
                'api_key' => $this->apiKey,
                'sender_id' => $this->senderId,
                'recipient_contact_no' => $recipient,
                'message' => $message,
            ]);

            return $this->buildResponse($response);
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }

    // Bulk SMS
    public function sendBulkSms(array $numbers, string $message, string $campaign = 'LaravelCampaign'): array
    {
        try {
            $formattedNumbers = array_map(
                fn($number) => $this->formatNumber((string) $number),
                $numbers
            );

            $response = Http::asForm()
                ->timeout(20)
                ->post("{$this->baseUrl}/bulk", [
                    'user_id' => $this->userId,
                    'api_key' => $this->apiKey,
                    'sender_id' => $this->senderId,
                    'campaign_name' => $campaign,
                    'message' => $message,
                    'recipient_contact_no' => implode(',', $formattedNumbers),
                ]);

            return $this->buildResponse($response);
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }

    // OTP SMS
    public function sendOtp(string $number): array
    {
        $otp = (string) random_int(100000, 999999);
        $message = "Your verification code is: {$otp}";

        $smsResponse = $this->sendSms($number, $message);

        return [
            'success' => $smsResponse['success'] ?? false,
            'otp' => $otp,
            'sms_response' => $smsResponse,
        ];
    }

    // Balance
    public function getBalance(): array
    {
        try {
            $response = Http::asForm()
                ->timeout(15)
                ->get("{$this->baseUrl}/getBalance", [
                    'user_id' => $this->userId,
                    'api_key' => $this->apiKey,
                ]);

            return $this->buildResponse($response);
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }

    private function buildResponse(Response $response): array
    {
        $json = $response->json();

        if (!is_array($json)) {
            return [
                'success' => false,
                'http_status' => $response->status(),
                'provider_status_code' => null,
                'provider_message' => 'Invalid response from SMS provider',
                'message_id' => null,
                'body' => $response->body(),
                'json' => null,
            ];
        }

        $providerStatusCode = isset($json['status_code']) ? (int) $json['status_code'] : null;
        $providerMessage = $this->getStatusMessage($providerStatusCode);
        $messageId = $json['msg_id'] ?? $json['message_id'] ?? null;

        return [
            'success' => $providerStatusCode === 204,
            'http_status' => $response->status(),
            'provider_status_code' => $providerStatusCode,
            'provider_message' => $providerMessage,
            'message_id' => $messageId,
            'body' => $response->body(),
            'json' => $json,
        ];
    }

    private function getStatusMessage(?int $statusCode): string
    {
        return self::STATUS_MESSAGES[$statusCode] ?? 'Unknown provider status';
    }

    private function exceptionResponse(\Throwable $e): array
    {
        return [
            'success' => false,
            'http_status' => null,
            'provider_status_code' => null,
            'provider_message' => 'Application error',
            'message_id' => null,
            'body' => null,
            'json' => null,
            'error' => $e->getMessage(),
        ];
    }

    // Format Sri Lanka numbers
    private function formatNumber(string $number): string
    {
        $number = preg_replace('/\D+/', '', trim($number));

        if (str_starts_with($number, '0')) {
            return '94' . substr($number, 1);
        }

        if (str_starts_with($number, '94')) {
            return $number;
        }

        return $number;
    }
}
