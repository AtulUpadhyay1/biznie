<?php

namespace App\Services\Chat\Nlu;

use App\Services\Chat\ChatContext;

interface IntentClassifier
{
    /**
     * @param  list<array{role: 'user'|'assistant', content: string}>  $history
     *         Earlier turns of this session, text only, oldest first.
     */
    public function classify(string $text, ChatContext $ctx, array $history = []): ?NluResult;
}
