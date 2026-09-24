<?php

namespace App\Services\Chat;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Sessions, the audit trail, idempotent replays and LLM history.
 *
 * Privacy: IPs are stored as an HMAC, tokens never reach this class, lead
 * form payloads (name / phone / email) are not persisted, and third-party
 * phone numbers are blanked out of stored blocks.
 */
class ChatLogger
{
    /** How many derived ids to walk before giving up on determinism. */
    private const MAX_REPLACEMENTS = 3;

    /**
     * The session for this request. A session is bound to whoever opened it:
     * if it belongs to someone else (or the caller is now a guest), a
     * replacement session is used and its id returned instead.
     *
     * The replacement id is derived, not random: the same requested id from
     * the same caller always maps to the same replacement, so a retried send
     * (same client_message_id) lands in the same session and is replayed
     * rather than run a second time.
     */
    public function resolveSession(string $requestedId, ?User $user, string $language, Request $request): ChatSession
    {
        $userId = $user ? (int) $user->getKey() : null;
        $id     = $requestedId;

        for ($attempt = 0; $attempt <= self::MAX_REPLACEMENTS; $attempt++) {
            $session = ChatSession::find($id) ?? $this->createSession($id, $userId, $language, $request);

            if ($session && $session->belongsToUser($userId)) {
                if (! $session->wasRecentlyCreated) {
                    $session->forceFill(['language' => $language, 'last_activity_at' => Carbon::now()])->save();
                }

                return $session;
            }

            $id = $this->replacementId($id, $userId);
        }

        // Practically unreachable (it takes a derived-id collision chain).
        return $this->createSession((string) Str::uuid(), $userId, $language, $request)
            ?? throw new \RuntimeException('Could not start a chat session.');
    }

    /**
     * UUID v5 of (requested id, caller) keyed with the app key: stable for a
     * retry, unguessable for anyone without the key, distinct per caller.
     */
    public function replacementId(string $requestedId, ?int $userId): string
    {
        $name = hash_hmac('sha256', strtolower($requestedId) . '|' . ($userId ?? 'guest'), (string) config('app.key'));

        return Uuid::uuid5(Uuid::NAMESPACE_OID, 'biznie-chat-session:' . $name)->toString();
    }

    /** null when the id was taken in the meantime (and the caller re-reads it). */
    private function createSession(string $id, ?int $userId, string $language, Request $request): ?ChatSession
    {
        try {
            return ChatSession::create([
                'id'               => $id,
                'user_id'          => $userId,
                'language'         => $language,
                'channel'          => 'web',
                'ip_hash'          => $this->hashIp((string) $request->ip()),
                'user_agent'       => Str::limit((string) $request->userAgent(), 250, ''),
                'last_activity_at' => Carbon::now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            // Two first messages raced on the same new id.
            return ChatSession::find($id);
        }
    }

    /**
     * The stored reply to an earlier send with the same client_message_id, or
     * null if this id has not been answered yet.
     *
     * @return array{intent: ?string, engine: string, messages: list<array>}|null
     */
    public function replay(ChatSession $session, string $clientMessageId): ?array
    {
        $turn = ChatMessage::where('chat_session_id', $session->id)
            ->where('client_message_id', $clientMessageId)
            ->first();

        if (! $turn) {
            return null;
        }

        $bots = ChatMessage::where('reply_to_id', $turn->id)->orderBy('created_at')->orderBy('id')->get();
        if ($bots->isEmpty()) {
            return null;
        }

        return [
            'intent'   => $turn->intent,
            'engine'   => (string) ($turn->engine ?: Blocks::ENGINE_ACTION),
            'messages' => $bots->map(fn (ChatMessage $m) => $this->present($m))->all(),
        ];
    }

    /**
     * Claims the client_message_id. Throws UniqueConstraintViolationException
     * when the same id is already being (or has been) handled.
     */
    public function logUserTurn(ChatSession $session, string $clientMessageId, ?string $text, ?array $action): ChatMessage
    {
        return ChatMessage::create([
            'chat_session_id'   => $session->id,
            'role'              => 'user',
            'client_message_id' => $clientMessageId,
            'text'              => $text,
            'action'            => $action ? $this->redactAction($action) : null,
        ]);
    }

    public function logBotTurn(ChatMessage $userTurn, ChatReply $reply, int $latencyMs): ChatMessage
    {
        $userTurn->forceFill(['intent' => $reply->intent, 'engine' => $reply->engine])->save();

        return ChatMessage::create([
            'chat_session_id' => $userTurn->chat_session_id,
            'role'            => 'bot',
            'reply_to_id'     => $userTurn->id,
            'intent'          => $reply->intent,
            'engine'          => $reply->engine,
            'blocks'          => $this->redactBlocks($reply->blocks),
            'latency_ms'      => max(0, $latencyMs),
        ]);
    }

    /** The wire shape of one bot message. */
    public function present(ChatMessage $message, ?array $blocks = null): array
    {
        return [
            'id'         => $message->id,
            'role'       => 'bot',
            'created_at' => optional($message->created_at)->toIso8601String(),
            'blocks'     => $blocks ?? $message->blocks ?? [],
        ];
    }

    /**
     * The last few turns of *this* session as plain text for the LLM. Bot
     * turns are reduced to the intent they answered, so no order, enquiry or
     * account data ever leaves the server.
     *
     * @return list<array{role: 'user'|'assistant', content: string}>
     */
    public function history(ChatSession $session, int $turns): array
    {
        $rows = ChatMessage::where('chat_session_id', $session->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id') // ordered UUIDs break same-second ties
            ->limit(max(0, $turns))
            ->get(['role', 'text', 'action', 'intent', 'created_at'])
            ->reverse()
            ->values();

        $history = [];
        foreach ($rows as $row) {
            if ($row->role === 'user') {
                $content = $row->text !== null && $row->text !== ''
                    ? $row->text
                    : '(selected option: ' . ($row->action->type ?? 'unknown') . ')';
                $history[] = ['role' => 'user', 'content' => $content];
            } else {
                $history[] = ['role' => 'assistant', 'content' => '(answered: ' . ($row->intent ?? 'fallback') . ')'];
            }
        }

        return $history;
    }

    public function hashIp(string $ip): string
    {
        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }

    /** Lead payloads carry contact details, so only the action type is kept. */
    private function redactAction(array $action): array
    {
        if (($action['type'] ?? null) === 'lead.submit') {
            return ['type' => 'lead.submit'];
        }

        return $action;
    }

    /** @param  list<array<string, mixed>>  $blocks */
    private function redactBlocks(array $blocks): array
    {
        return array_map(function (array $block) {
            if (($block['type'] ?? null) === 'lead_form') {
                $block['prefill'] = (object) [];
            }
            if (($block['type'] ?? null) === 'order_detail' && isset($block['order']['tracking'])) {
                foreach ($block['order']['tracking'] as $i => $row) {
                    $block['order']['tracking'][$i]['driver_phone'] = null;
                }
            }

            return $block;
        }, $blocks);
    }
}
