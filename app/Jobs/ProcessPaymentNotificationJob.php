<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    protected Payment $payment;
    protected string $notificationClass;
    protected ?string $event;
    protected ?array $additionalData;

    public function __construct(
        Payment $payment,
        string $notificationClass,
        ?string $event = null,
        ?array $additionalData = null
    ) {
        $this->payment = $payment;
        $this->notificationClass = $notificationClass;
        $this->event = $event;
        $this->additionalData = $additionalData;
        $this->onQueue('payments');
    }

    public function handle(): void
    {
        try {
            $notification = new $this->notificationClass($this->payment);

            if ($this->additionalData) {
                foreach ($this->additionalData as $key => $value) {
                    $notification->$key = $value;
                }
            }

            $this->payment->user->notify($notification);

            if ($this->event) {
                AuditLog::log($this->event, $this->payment, null, [
                    'notification_sent' => true,
                    'notification_class' => $this->notificationClass,
                    'payment_id' => $this->payment->id,
                ]);
            }

            Log::info('Payment notification sent successfully', [
                'payment_id' => $this->payment->id,
                'notification' => $this->notificationClass,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment notification', [
                'payment_id' => $this->payment->id,
                'notification' => $this->notificationClass,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Payment notification job failed permanently', [
            'payment_id' => $this->payment->id,
            'notification' => $this->notificationClass,
            'error' => $exception->getMessage(),
        ]);
    }
}
