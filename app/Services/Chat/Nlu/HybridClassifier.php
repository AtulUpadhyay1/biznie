<?php

namespace App\Services\Chat\Nlu;

use App\Services\Chat\ChatContext;

/**
 * Rules first, LLM second (plan §5.2).
 *
 * 1. A reference matched, or the rules are confident → rules.
 * 2. Otherwise, if the LLM is enabled → Claude.
 * 3. LLM failed, timed out or refused → the rule result (which is `fallback`
 *    when the rules found nothing either).
 */
class HybridClassifier implements IntentClassifier
{
    public function __construct(
        private readonly RuleBasedClassifier $rules,
        private readonly ClaudeClassifier $llm,
    ) {
    }

    public function classify(string $text, ChatContext $ctx, array $history = []): NluResult
    {
        $rule = $this->rules->classify($text, $ctx, $history);

        if ($rule->hasReference() || $rule->confidence >= RuleBasedClassifier::CONFIDENT) {
            return $rule;
        }

        if (! $this->llm->enabled()) {
            return $rule;
        }

        $llm = $this->llm->classify($text, $ctx, $history);
        if (! $llm) {
            return $rule;
        }

        // Regex entities (a quantity, a city) the model missed still count.
        return $llm->withEntitiesFrom($rule);
    }
}
