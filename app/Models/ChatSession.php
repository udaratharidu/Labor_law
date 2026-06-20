<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'chat_title',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // chats.chat_session_id references chat_sessions.session_id
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'chat_session_id', 'session_id');
    }

    public function summary(): HasOne
    {
        return $this->hasOne(ChatSummary::class, 'chat_session_id', 'id');
    }
}
