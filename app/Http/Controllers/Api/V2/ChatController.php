<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\User;
use App\Services\Chat\ChatContext;
use App\Services\Chat\ChatEngine;
use App\Services\Chat\ChatLogger;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Biznie AI — one endpoint for free text and button presses alike.
 *
 * Public route: the user is resolved from the bearer token if there is one,
 * and a missing, invalid or expired token simply means "guest" (never 401).
 * `(session_id, client_message_id)` makes every send idempotent — a retry
 * gets the stored reply and nothing runs twice.
 */
class ChatController extends Controller
{
    public function __construct(
        private readonly ChatEngine $engine,
        private readonly ChatLogger $logger,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $startedAt = microtime(true);
        $asked     = $request->input('language');
        $utf8      = $this->validUtf8(in_array($asked, config('biznie.chat.languages'), true) ? $asked : 'en');

        $data = $request->validate([
            'session_id'        => ['required', 'string', 'uuid'],
            'client_message_id' => ['required', 'string', 'uuid'],
            'language'          => ['nullable', 'string', Rule::in(config('biznie.chat.languages'))],
            'message'           => ['nullable', 'string', $utf8, 'max:1000'],
            'action'            => ['nullable', 'array', $utf8],
            'action.type'       => ['required_with:action', 'string', 'max:40'],
            'action.payload'    => ['nullable', 'array'],
        ]);

        $text    = isset($data['message']) ? trim($data['message']) : '';
        $action  = ! empty($data['action']) ? $data['action'] : null;
        $payload = is_array($action['payload'] ?? null) ? $action['payload'] : [];

        if (($text === '') === ($action === null)) {
            throw ValidationException::withMessages([
                'message' => [__('chat.validation_one_of', [], $data['language'] ?? 'en')],
            ]);
        }
        if (strlen((string) json_encode($payload)) > 5000) {
            throw ValidationException::withMessages([
                'action.payload' => [__('chat.validation_payload', [], $data['language'] ?? 'en')],
            ]);
        }

        $user     = $this->resolveUser();
        $language = $data['language'] ?? 'en';
        $session  = $this->logger->resolveSession($data['session_id'], $user, $language, $request);
        $clientId = $data['client_message_id'];

        // A retried send: hand back what was already answered.
        if ($stored = $this->logger->replay($session, $clientId)) {
            return $this->respond($session, $clientId, $user, $stored);
        }

        $ctx = new ChatContext(
            $user,
            $language,
            $session,
            $user ? 'u:' . $user->getKey() : 'ip:' . $this->logger->hashIp((string) $request->ip()),
        );

        $type = $action ? (string) $action['type'] : null;

        // Writes are capped per hour. A guest's enquiry.submit only ever gets
        // "please log in" back, so it is not a write and costs nothing.
        $capped = $type !== null
            && in_array($type, ChatEngine::WRITE_ACTIONS, true)
            && ($user !== null || ! in_array($type, ChatEngine::AUTH_ACTIONS, true));
        $capKey = 'chat-write:' . $ctx->clientKey;

        if ($capped) {
            // Take the slot first (an atomic increment), so concurrent submits
            // cannot all pass a check before any of them counts; it is handed
            // back below if nothing ends up being created.
            $hits = RateLimiter::hit($capKey, 3600);
            if ($hits > (int) config('biznie.chat.rate_limits.writes_per_hour', 10)) {
                $this->refundWrite($capKey);

                return response()->json([
                    'success'     => false,
                    'message'     => $ctx->t('rate_limited_writes'),
                    'retry_after' => RateLimiter::availableIn($capKey),
                ], 429);
            }
        }

        // Free text is understood *before* any transaction is opened: the LLM
        // can take seconds and must never hold row locks while it does.
        $nlu = null;
        if ($text !== '') {
            $history = ! $session->wasRecentlyCreated && config('biznie.chat.llm.enabled')
                ? $this->logger->history($session, (int) config('biznie.chat.llm.history_turns', 6))
                : [];
            $nlu = $this->engine->classify($ctx, $text, $history);
        }

        try {
            // Claim + handle + answer commit together: a failed turn leaves no
            // claimed id behind (so it can be retried) and no half-made RFQ.
            // Pushes to sellers / admins are held back until after the commit.
            $result = DB::transaction(function () use ($ctx, $session, $clientId, $text, $action, $type, $payload, $nlu, $startedAt) {
                // Claims the id first: a concurrent duplicate blocks here and
                // then fails on the unique index instead of running twice.
                $turn = $this->logger->logUserTurn(
                    $session,
                    $clientId,
                    $text !== '' ? $text : null,
                    $action ? ['type' => $type, 'payload' => $payload] : null,
                );

                $reply = $nlu !== null
                    ? $this->engine->handleUnderstood($ctx, $nlu)
                    : $this->engine->handleAction($ctx, $type, $payload);

                $bot = $this->logger->logBotTurn($turn, $reply, (int) round((microtime(true) - $startedAt) * 1000));

                return [
                    'intent'   => $reply->intent,
                    'engine'   => $reply->engine,
                    // The live reply, not the privacy-redacted stored copy.
                    'messages' => [$this->logger->present($bot, $reply->blocks)],
                ];
            });
        } catch (UniqueConstraintViolationException) {
            $ctx->discardAfterCommit();
            if ($capped) {
                $this->refundWrite($capKey);
            }

            if ($stored = $this->logger->replay($session, $clientId)) {
                return $this->respond($session, $clientId, $user, $stored);
            }

            return response()->json([
                'success' => false,
                'message' => $ctx->t('error_in_progress'),
            ], 409);
        } catch (\Throwable $e) {
            // Rolled back: nothing was created, so nothing is announced and
            // the write slot is handed back (a 422 is the common case).
            $ctx->discardAfterCommit();
            if ($capped) {
                $this->refundWrite($capKey);
            }

            throw $e;
        }

        // Committed: now bidding opens and sellers / admins are told.
        $ctx->runAfterCommit();

        return $this->respond($session, $clientId, $user, $result);
    }

    /** Gives back a write slot taken by a send that created nothing. */
    private function refundWrite(string $capKey): void
    {
        if ((int) RateLimiter::attempts($capKey) > 0) {
            RateLimiter::increment($capKey, 3600, -1);
        }
    }

    /**
     * Rejects strings that are not valid UTF-8 anywhere in the value
     * (nested payload keys included) with a 422, instead of a 500 when the
     * turn is later JSON-encoded or stored.
     */
    private function validUtf8(string $language): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($language): void {
            if (! self::isValidUtf8($value)) {
                $fail(__('chat.validation_encoding', [], $language));
            }
        };
    }

    private static function isValidUtf8(mixed $value): bool
    {
        if (is_string($value)) {
            return mb_check_encoding($value, 'UTF-8');
        }
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (! self::isValidUtf8((string) $key) || ! self::isValidUtf8($item)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * The optional user behind the bearer token. Anything that is not a
     * customer-side User (or no / a bad token) is a guest.
     */
    private function resolveUser(): ?User
    {
        try {
            $user = auth('sanctum')->user();
        } catch (\Throwable) {
            return null;
        }

        return $user instanceof User ? $user : null;
    }

    /** @param  array{intent: ?string, engine: string, messages: list<array>}  $result */
    private function respond(ChatSession $session, string $clientId, ?User $user, array $result): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'session_id'    => $session->id,
                'reply_to'      => $clientId,
                'authenticated' => $user !== null,
                'intent'        => $result['intent'],
                'engine'        => $result['engine'],
                'messages'      => $result['messages'],
            ],
        ]);
    }
}
