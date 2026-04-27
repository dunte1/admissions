<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendApplicationNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    protected Application $application;
    protected string $notificationClass;
    protected ?string $event;
    protected ?array $additionalData;

    public function __construct(
        Application $application,
        string $notificationClass,
        ?string $event = null,
        ?array $additionalData = null
    ) {
        $this->application = $application;
        $this->notificationClass = $notificationClass;
        $this->event = $event;
        $this->additionalData = $additionalData;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            $notification = new $this->notificationClass($this->application);

            if ($this->additionalData) {
                foreach ($this->additionalData as $key => $value) {
                    $notification->$key = $value;
                }
            }

            $this->application->user->notify($notification);

            if ($this->event) {
                AuditLog::log($this->event, $this->application, null, [
                    'notification_sent' => true,
                    'notification_class' => $this->notificationClass,
                ]);
            }

            Log::info('Application notification sent successfully', [
                'application_id' => $this->application->id,
                'notification' => $this->notificationClass,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send application notification', [
                'application_id' => $this->application->id,
                'notification' => $this->notificationClass,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Application notification job failed permanently', [
            'application_id' => $this->application->id,
            'notification' => $this->notificationClass,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
