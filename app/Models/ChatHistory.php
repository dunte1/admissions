<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'mode',
        'message',
        'response',
        'context',
        'tokens_used',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'context' => 'array',
        'tokens_used' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function saveConversation($userId, $sessionId, $mode, $message, $response, $context = [], $tokensUsed = 0)
    {
        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'mode' => $mode,
            'message' => $message,
            'response' => $response,
            'context' => $context,
            'tokens_used' => $tokensUsed,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function getUserHistory($userId, $limit = 50)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getSessionHistory($sessionId, $limit = 20)
    {
        return static::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }
}
