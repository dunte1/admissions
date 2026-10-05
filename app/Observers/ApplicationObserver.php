<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\AuditLog;
use App\Jobs\SendApplicationNotificationJob;
use App\Notifications\ApplicationSubmitted;
use App\Notifications\ApplicationApproved;
use App\Notifications\ApplicationRejected;
use App\Notifications\ApplicationInfoRequested;
use Illuminate\Support\Facades\Log;

class ApplicationObserver
{
    public function created(Application $application): void
    {
        if (!$application->user) return;

        try {
            SendApplicationNotificationJob::dispatch(
                $application,
                ApplicationSubmitted::class,
                'notification_sent',
                ['event' => 'submitted']
            );
        } catch (\Exception $e) {
            Log::error('Failed to queue application submitted notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function updated(Application $application): void
    {
        if (!$application->user) return;

        $originalStatus = $application->getOriginal('status');
        $newStatus = $application->status;

        if ($originalStatus === $newStatus) return;

        try {
            switch ($newStatus) {
                case 'approved':
                    SendApplicationNotificationJob::dispatch(
                        $application,
                        ApplicationApproved::class,
                        'notification_sent'
                    );
                    break;

                case 'rejected':
                    SendApplicationNotificationJob::dispatch(
                        $application,
                        ApplicationRejected::class,
                        'notification_sent'
                    );
                    break;

                case 'info_requested':
                    SendApplicationNotificationJob::dispatch(
                        $application,
                        ApplicationInfoRequested::class,
                        'notification_sent'
                    );
                    break;
            }

            AuditLog::log('application_status_changed', $application, null, [
                'from' => $originalStatus,
                'to' => $newStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue application status notification', [
                'application_id' => $application->id,
                'new_status' => $newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
