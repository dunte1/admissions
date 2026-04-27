<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\Broadcast;
use App\Models\BroadcastRecipient;
use App\Models\BroadcastLog;
use App\Jobs\SendBroadcastJob;
use App\Jobs\SendApplicationNotificationJob;
use App\Notifications\ApplicationSubmitted;
use App\Notifications\ApplicationApproved;
use App\Notifications\ApplicationRejected;
use App\Notifications\ApplicationInfoRequested;
use Illuminate\Support\Facades\Notification;
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
            
            $this->logNotification($application, 'submitted');
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
                    $this->logNotification($application, 'approved');
                    break;

                case 'rejected':
                    SendApplicationNotificationJob::dispatch(
                        $application,
                        ApplicationRejected::class,
                        'notification_sent'
                    );
                    $this->logNotification($application, 'rejected');
                    break;

                case 'info_requested':
                    SendApplicationNotificationJob::dispatch(
                        $application,
                        ApplicationInfoRequested::class,
                        'notification_sent'
                    );
                    $this->logNotification($application, 'info_requested');
                    break;

                case 'under_review':
                    $this->sendBulkNotification($application, 'under_review', 'Application Under Review');
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to queue application status notification', [
                'application_id' => $application->id,
                'new_status' => $newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function logNotification(Application $application, string $type): void
    {
        $broadcast = Broadcast::create([
            'user_id' => auth()->id() ?? 1,
            'school_id' => $application->school_id,
            'title' => "Application {$type}",
            'message' => "Notification sent for application #{$application->application_number}",
            'type' => 'in_app',
            'targeting' => [
                'schools' => 'all',
                'roles' => [],
                'application_status' => [],
            ],
            'status' => 'sent',
            'sent_at' => now(),
            'total_recipients' => 1,
            'sent_count' => 1,
            'delivered_count' => 1,
        ]);

        BroadcastRecipient::create([
            'broadcast_id' => $broadcast->id,
            'user_id' => $application->user_id,
            'school_id' => $application->school_id,
            'status' => 'delivered',
            'sent_at' => now(),
            'delivered_at' => now(),
        ]);

        BroadcastLog::created($broadcast);
    }

    protected function sendBulkNotification(Application $application, string $status, string $defaultTitle): void
    {
        $template = $this->getNotificationTemplate($application->school_id, $status);
        
        if (!$template) return;

        $title = $template->title ?? $defaultTitle;
        $message = $this->parseTemplate($template->message ?? '', $application);

        $recipients = $this->getRecipientsForStatus($application, $status);

        if ($recipients->isEmpty()) return;

        $broadcast = Broadcast::create([
            'user_id' => auth()->id() ?? 1,
            'school_id' => $application->school_id,
            'title' => $title,
            'message' => $message,
            'type' => 'in_app',
            'targeting' => [
                'schools' => [$application->school_id],
                'roles' => [],
                'application_status' => [$status],
            ],
            'status' => 'draft',
            'total_recipients' => $recipients->count(),
        ]);

        foreach ($recipients as $user) {
            BroadcastRecipient::create([
                'broadcast_id' => $broadcast->id,
                'user_id' => $user->id,
                'school_id' => $application->school_id,
                'status' => 'pending',
            ]);
        }

        BroadcastLog::created($broadcast);
        SendBroadcastJob::dispatch($broadcast);
    }

    protected function getRecipientsForStatus(Application $application, string $status): \Illuminate\Support\Collection
    {
        return \App\Models\User::query()
            ->where('school_id', $application->school_id)
            ->where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'registrar', 'reviewer']);
            })
            ->get();
    }

    protected function getNotificationTemplate($schoolId, string $event): ?object
    {
        $templates = [
            'submitted' => (object)[
                'title' => 'New Application Submitted',
                'message' => 'A new application (#{{application_number}}) has been submitted for {{program_name}}.',
            ],
            'approved' => (object)[
                'title' => 'Application Approved',
                'message' => 'Congratulations! Your application (#{{application_number}}) for {{program_name}} has been approved.',
            ],
            'rejected' => (object)[
                'title' => 'Application Update',
                'message' => 'We regret to inform you that your application (#{{application_number}}) for {{program_name}} has not been successful.',
            ],
            'info_requested' => (object)[
                'title' => 'Additional Information Required',
                'message' => 'Your application (#{{application_number}}) for {{program_name}} requires additional information. Please check your portal for details.',
            ],
            'under_review' => (object)[
                'title' => 'Application Under Review',
                'message' => 'Your application (#{{application_number}}) for {{program_name}} is now under review.',
            ],
        ];

        return $templates[$event] ?? null;
    }

    protected function parseTemplate(string $template, Application $application): string
    {
        $variables = [
            '{{name}}' => $application->user?->fullName() ?? 'Student',
            '{{application_number}}' => $application->application_number,
            '{{program_name}}' => $application->program?->name ?? 'the program',
            '{{school_name}}' => $application->school?->name ?? '',
            '{{status}}' => ucfirst(str_replace('_', ' ', $application->status)),
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }
}
