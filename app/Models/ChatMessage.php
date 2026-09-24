<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $fillable = [
        'chat_session_id',
        'role',
        'client_message_id',
        'reply_to_id',
        'intent',
        'engine',
        'text',
        'action',
        'blocks',
        'latency_ms',
    ];

    /**
     * `object`, not `array`: blocks are replayed verbatim on a retried send,
     * and decoding to stdClass keeps `{}` from coming back as `[]`.
     */
    protected $casts = [
        'action'     => 'object',
        'blocks'     => 'object',
        'latency_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }
}
