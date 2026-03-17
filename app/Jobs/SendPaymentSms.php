<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPaymentSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    public $backoff = [10, 30, 60];

    protected $guardianNumber;
    protected $message;

    public function __construct($guardianNumber, $message)
    {
        $this->guardianNumber = $guardianNumber;
        $this->message = $message;
    }

    public function handle(SmsService $smsService)
    {
        try {
            $response = $smsService->sendSms($this->guardianNumber, $this->message);

            Log::info('SMS sent successfully', [
                'guardian_number' => $this->guardianNumber,
                'response' => $response
            ]);
        } catch (\Throwable $e) {
            Log::error('SMS sending failed', [
                'guardian_number' => $this->guardianNumber,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'will_retry' => $this->attempts() < $this->tries
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('SMS job permanently failed after ' . $this->attempts() . ' attempts', [
            'guardian_number' => $this->guardianNumber,
            'error' => $exception->getMessage()
        ]);
    }
}
