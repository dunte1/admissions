<?php

namespace App\Jobs;

use App\Models\Broadcast;
use App\Models\BroadcastLog;
use App\Models\BroadcastRecipient;
use App\Models\User;
use App\Notifications\SystemBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 3600;

    protected Broadcast $broadcast;
    protected ?\Illuminate\Support\Collection $specificRecipients;

    public function __construct(Broadcast $broadcast, ?\Illuminate\Support\Collection $specificRecipients = null)
    {
        $this->broadcast = $broadcast;
        $this->specificRecipients = $specificRecipients;
    }

    public function handle(): void
    {
        try {
            $this->broadcast->markAsSending();

            $recipients = $this->specificRecipients 
                ?? BroadcastRecipient::where('broadcast_id', $this->broadcast->id)
                    ->where('status', 'pending')
                    ->with('user')
                    ->get();

            BroadcastLog::sending($this->broadcast, $recipients->count());

            $sentCount = 0;
            $failedCount = 0;

            foreach ($recipients as $recipient) {
                try {
                    $this->sendToRecipient($recipient);
                    $sentCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $recipient->markAsFailed($e->getMessage());
                    Log::error('Broadcast send failed', [
                        'broadcast_id' => $this->broadcast->id,
                        'user_id' => $recipient->user_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->broadcast->update([
                'sent_count' => $this->broadcast->sent_count + $sentCount,
                'failed_count' => $this->broadcast->failed_count + $failedCount,
            ]);

            if ($failedCount === 0 && $sentCount > 0) {
                $this->broadcast->markAsSent();
                BroadcastLog::sent($this->broadcast);
            } elseif ($failedCount > 0 && $sentCount === 0) {
                $this->broadcast->markAsFailed();
                BroadcastLog::failed($this->broadcast, 'All recipients failed');
            } else {
                $this->broadcast->update(['status' => 'sent']);
                BroadcastLog::sent($this->broadcast);
            }

        } catch (\Exception $e) {
            $this->broadcast->markAsFailed();
            BroadcastLog::failed($this->broadcast, $e->getMessage());
            Log::error('Broadcast job failed', [
                'broadcast_id' => $this->broadcast->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function sendToRecipient(BroadcastRecipient $recipient): void
    {
        $user = $recipient->user;

        if (!$user) {
            throw new \Exception('User not found');
        }

        $parsedTitle = Broadcast::parseVariables($this->broadcast->title, [
            'name' => $user->fullName(),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'school_name' => $user->school?->name,
        ]);

        $parsedMessage = Broadcast::parseVariables($this->broadcast->message, [
            'name' => $user->fullName(),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'school_name' => $user->school?->name,
        ]);

        $channels = $this->getChannels();

        foreach ($channels as $channel) {
            try {
                if ($channel === 'database') {
                    $user->notify(new SystemBroadcast($parsedTitle, $parsedMessage));
                } elseif ($channel === 'mail') {
                    $user->notify(new SystemBroadcast($parsedTitle, $parsedMessage));
                }
            } catch (\Exception $e) {
                Log::warning("Failed to send via {$channel}", [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $recipient->markAsSent();

        if ($this->broadcast->type !== 'sms') {
            $recipient->markAsDelivered();
        }
    }

    protected function getChannels(): array
    {
        return match($this->broadcast->type) {
            'in_app' => ['database'],
            'email' => ['mail'],
            'sms' => ['vonage'],
            'all' => ['database', 'mail'],
            default => ['database'],
        };
    }

    public function failed(\Throwable $exception): void
    {
        $this->broadcast->markAsFailed();
        BroadcastLog::failed($this->broadcast, 'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage());
    }
}
