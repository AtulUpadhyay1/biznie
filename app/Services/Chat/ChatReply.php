<?php

namespace App\Services\Chat;

/** What the engine hands back for one turn: one bot message worth of blocks. */
final class ChatReply
{
    /**
     * @param  list<array<string, mixed>>  $blocks
     */
    public function __construct(
        public readonly ?string $intent,
        public readonly string $engine,
        public readonly array $blocks,
    ) {
    }
}
