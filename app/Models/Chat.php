<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $fillable = [
        'user_message',
        'ai_response',
        'chat_session_id',
        'is_summarized',
    ];

    protected $casts = [
        'is_summarized' => 'boolean',
    ];

    // chat_session_id is the string session_id from chat_sessions
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id', 'session_id');
    }
}
