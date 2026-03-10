<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SmsService;
use Exception;
use Illuminate\Support\Facades\Log;

class SendPaymentSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

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
            // Send SMS
            $smsService->sendSms($this->guardianNumber, $this->message);
            
        } catch (Exception $e) {
            Log::error('❌ SMS sending failed', [
                'guardian_number' => $this->guardianNumber,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'will_retry' => $this->attempts() < $this->tries
            ]);
            throw $e;
        }
    }

    public function failed(Exception $exception)
    {
        Log::error('💥 SMS job permanently failed after ' . $this->attempts() . ' attempts', [
            'guardian_number' => $this->guardianNumber,
            'error' => $exception->getMessage()
        ]);
    }
}