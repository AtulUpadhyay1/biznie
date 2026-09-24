<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'language',
        'channel',
        'ip_hash',
        'user_agent',
        'last_activity_at',
    ];

    protected $casts = [
        'user_id'          => 'integer',
        'last_activity_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_session_id');
    }

    /** A session only ever serves the user (or guest) that opened it. */
    public function belongsToUser(?int $userId): bool
    {
        return $this->user_id === $userId;
    }
}
