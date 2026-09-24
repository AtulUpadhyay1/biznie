<?php

namespace App\Services\Chat\Handlers;

use App\Services\Chat\Blocks;
use App\Services\Chat\ChatContext;

/**
 * Thanks, credit questions and "didn't understand". When the LLM supplied a
 * short reply (already validated as plain text) it is used; otherwise the
 * localised canned copy is.
 */
class SmallTalkHandler
{
    public function __construct(private readonly MenuHandler $menu)
    {
    }

    public function thanks(ChatContext $ctx, ?string $reply = null): array
    {
        return [
            Blocks::text($reply ?: $ctx->t('thanks')),
            $this->menu->backToMenu($ctx),
        ];
    }

    public function credit(ChatContext $ctx, ?string $reply = null): array
    {
        return [
            Blocks::text($reply ?: $ctx->t('credit_info')),
            $this->menu->backToMenu($ctx, [
                Blocks::option($ctx->t('menu.support'), Blocks::action('support.contact')),
            ]),
        ];
    }

    public function fallback(ChatContext $ctx, ?string $reply = null): array
    {
        return [
            Blocks::text($reply ?: $ctx->t('fallback')),
            $this->menu->mainMenu($ctx),
        ];
    }
}
