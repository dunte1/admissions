<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'broadcast_id',
        'action',
        'details',
        'ip_address',
    ];

    public function broadcast(): BelongsTo
    {
        return $this->belongsTo(Broadcast::class);
    }

    public static function log(Broadcast $broadcast, string $action, ?string $details = null): self
    {
        return self::create([
            'broadcast_id' => $broadcast->id,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    public static function logCreated(Broadcast $broadcast): self
    {
        return self::log($broadcast, 'created', 'Broadcast created');
    }

    public static function scheduled(Broadcast $broadcast, string $scheduledAt): self
    {
        return self::log($broadcast, 'scheduled', "Scheduled for {$scheduledAt}");
    }

    public static function sending(Broadcast $broadcast, int $recipientCount): self
    {
        return self::log($broadcast, 'sending', "Sending to {$recipientCount} recipients");
    }

    public static function sent(Broadcast $broadcast): self
    {
        return self::log($broadcast, 'sent', 'Broadcast sent successfully');
    }

    public static function failed(Broadcast $broadcast, string $error): self
    {
        return self::log($broadcast, 'failed', $error);
    }

    public static function cancelled(Broadcast $broadcast): self
    {
        return self::log($broadcast, 'cancelled', 'Broadcast cancelled');
    }
}
