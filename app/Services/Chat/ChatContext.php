<?php

namespace App\Services\Chat;

use App\Models\ChatSession;
use App\Models\User;

/**
 * Everything one chat turn needs to know about who is asking.
 *
 * `ownerId()` is the only id any order/enquiry query may be scoped by: staff
 * sub-users act on their owner's account, exactly like the dashboard screens.
 */
final class ChatContext
{
    /** @var list<\Closure> side effects held back until the turn has committed */
    private array $afterCommit = [];

    public function __construct(
        public readonly ?User $user,
        public readonly string $language,
        public readonly ChatSession $session,
        public readonly string $clientKey,
    ) {
    }

    public function authenticated(): bool
    {
        return $this->user !== null;
    }

    public function ownerId(): ?int
    {
        if (! $this->user) {
            return null;
        }

        return $this->user->is_staff ? (int) $this->user->added_by : (int) $this->user->id;
    }

    /** Localised bot copy from lang/{language}/chat.php. */
    public function t(string $key, array $replace = []): string
    {
        return (string) __('chat.' . $key, $replace, $this->language);
    }

    public function choice(string $key, int $count, array $replace = []): string
    {
        return (string) trans_choice('chat.' . $key, $count, $replace, $this->language);
    }

    /**
     * Holds back an outside-world side effect (seller fan-out, pushes) until
     * the turn's transaction has committed. If the turn rolls back, it never
     * runs — nobody is told about a row that no longer exists.
     */
    public function afterCommit(\Closure $callback): void
    {
        $this->afterCommit[] = $callback;
    }

    /** Runs (once) what afterCommit() held back. Each callback is isolated. */
    public function runAfterCommit(): void
    {
        $callbacks         = $this->afterCommit;
        $this->afterCommit = [];

        foreach ($callbacks as $callback) {
            try {
                $callback();
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /** Drops held-back side effects, for a turn that rolled back. */
    public function discardAfterCommit(): void
    {
        $this->afterCommit = [];
    }
}
