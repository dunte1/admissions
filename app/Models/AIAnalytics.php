<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIAnalytics extends Model
{
    use HasFactory;
    
    protected $table = 'ai_analytics';

    protected $fillable = [
        'school_id',
        'user_id',
        'event_type',
        'session_id',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function track($eventType, $schoolId = null, $userId = null, $metadata = [], $sessionId = null)
    {
        return static::create([
            'school_id' => $schoolId,
            'user_id' => $userId,
            'event_type' => $eventType,
            'session_id' => $sessionId,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function getStats($schoolId, $startDate = null, $endDate = null)
    {
        $query = static::where('school_id', $schoolId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_chats' => $query->where('event_type', 'chat_started')->count(),
            'total_messages' => $query->where('event_type', 'message_sent')->count(),
            'total_escalations' => $query->where('event_type', 'escalation')->count(),
            'conversions' => $query->where('event_type', 'conversion')->count(),
            'daily_stats' => $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
        ];
    }
}
