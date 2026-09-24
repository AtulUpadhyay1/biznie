<?php

namespace App\Services\Chat;

use App\Services\Chat\Handlers\EnquiryHandler;
use App\Services\Chat\Handlers\LeadHandler;
use App\Services\Chat\Handlers\MenuHandler;
use App\Services\Chat\Handlers\OrderHandler;
use App\Services\Chat\Handlers\ProductHandler;
use App\Services\Chat\Handlers\SmallTalkHandler;
use App\Services\Chat\Handlers\SupportHandler;
use App\Services\Chat\Nlu\IntentClassifier;
use App\Services\Chat\Nlu\NluResult;
use Illuminate\Validation\ValidationException;

/**
 * Runs one turn: free text is classified and mapped onto an action; a button
 * press *is* an action. Either way the same handlers run, behind the same
 * auth gate.
 *
 * NLU output can only ever select a read or pre-fill a form. Writes
 * (`enquiry.submit`, `lead.submit`) are reachable only as explicit actions.
 */
class ChatEngine
{
    /** Actions that need a signed-in user. */
    public const AUTH_ACTIONS = [
        'order.list',
        'order.status',
        'enquiry.list',
        'enquiry.status',
        'enquiry.start',
        'enquiry.submit',
    ];

    /** Actions that create something, capped separately (plan §4.1). */
    public const WRITE_ACTIONS = ['enquiry.submit', 'lead.submit'];

    public function __construct(
        private readonly IntentClassifier $classifier,
        private readonly MenuHandler $menu,
        private readonly OrderHandler $orders,
        private readonly EnquiryHandler $enquiries,
        private readonly ProductHandler $products,
        private readonly LeadHandler $leads,
        private readonly SupportHandler $support,
        private readonly SmallTalkHandler $smallTalk,
    ) {
    }

    public function handleAction(ChatContext $ctx, string $type, array $payload): ChatReply
    {
        $blocks = $this->run($ctx, $type, $payload);

        if ($blocks === null) {
            return new ChatReply(null, Blocks::ENGINE_ACTION, [
                Blocks::error($ctx->t('error_unknown_action')),
                $this->menu->mainMenu($ctx),
            ]);
        }

        return new ChatReply($type, Blocks::ENGINE_ACTION, $blocks);
    }

    /**
     * Understands one line of free text. This may call the LLM (seconds), so
     * the controller runs it before any database transaction is opened.
     *
     * @param  list<array{role: 'user'|'assistant', content: string}>  $history
     */
    public function classify(ChatContext $ctx, string $text, array $history = []): NluResult
    {
        return $this->classifier->classify($text, $ctx, $history) ?? NluResult::fallback();
    }

    /**
     * @param  list<array{role: 'user'|'assistant', content: string}>  $history
     */
    public function handleMessage(ChatContext $ctx, string $text, array $history = []): ChatReply
    {
        return $this->handleUnderstood($ctx, $this->classify($ctx, $text, $history));
    }

    /** Answers free text that has already been classified. */
    public function handleUnderstood(ChatContext $ctx, NluResult $nlu): ChatReply
    {
        try {
            $blocks = match ($nlu->intent) {
                'greeting'    => $this->menu->bootstrap($ctx),
                'menu'        => $this->menu->menu($ctx),
                'thanks'      => $this->smallTalk->thanks($ctx, $nlu->reply),
                'credit.info' => $this->smallTalk->credit($ctx, $nlu->reply),
                'fallback'    => $this->smallTalk->fallback($ctx, $nlu->reply),
                default       => $this->run($ctx, ...$this->actionFor($nlu)),
            } ?? $this->smallTalk->fallback($ctx);
        } catch (ValidationException) {
            // Entities come from free text, not a form: an odd one must never
            // surface as a 422 on a chat message. Answer like "not understood".
            return new ChatReply('fallback', $nlu->engine, $this->smallTalk->fallback($ctx));
        }

        return new ChatReply($nlu->intent, $nlu->engine, $blocks);
    }

    /**
     * Understood intent → the action a button would have sent.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function actionFor(NluResult $nlu): array
    {
        return match ($nlu->intent) {
            'order.status'    => $nlu->entity('order_ref')
                ? ['order.status', ['ref' => $nlu->entity('order_ref')]]
                : ['order.list', ['scope' => 'active']],
            'order.list'      => ['order.list', ['scope' => 'all']],
            'enquiry.status'  => $nlu->entity('enquiry_ref')
                ? ['enquiry.status', ['ref' => $nlu->entity('enquiry_ref')]]
                : ['enquiry.list', ['scope' => 'open']],
            'enquiry.list'    => ['enquiry.list', ['scope' => 'all']],
            // Free text only ever pre-fills the form; the user submits it.
            'enquiry.create'  => ['enquiry.start', array_filter([
                'product_query' => $nlu->entity('product_query'),
                'quantity'      => $nlu->entity('quantity'),
                'unit_label'    => $nlu->entity('unit'),
                'delivery_city' => $nlu->entity('delivery_city'),
                'required_by'   => $nlu->entity('required_by'),
            ], fn ($v) => $v !== null)],
            'product.search'  => ['product.search', array_filter(['query' => $nlu->entity('product_query')])],
            'support.contact' => ['support.contact', []],
            default           => ['menu', []],
        };
    }

    /**
     * What the client replays after the user signs in. A read resumes as is;
     * a write never does — it reopens its form, pre-filled with what was
     * submitted, so creating anything always takes a fresh, explicit submit.
     */
    private function resumeAction(string $type, array $payload): array
    {
        return match ($type) {
            'enquiry.submit' => Blocks::action('enquiry.start', self::enquiryPrefill($payload)),
            'lead.submit'    => Blocks::action('lead.start'),
            default          => in_array($type, self::WRITE_ACTIONS, true)
                ? Blocks::action('menu')
                : Blocks::action($type, $payload),
        };
    }

    /**
     * An `enquiry.submit` payload → a safe `enquiry.start` prefill: only the
     * form's fields, each already within enquiry.start's validation rules, so
     * resuming can never itself produce a 422.
     */
    private static function enquiryPrefill(array $payload): array
    {
        $string = function (mixed $value, int $max): ?string {
            if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
                return null;
            }
            $value = trim(mb_substr((string) $value, 0, $max));

            return $value === '' ? null : $value;
        };
        $id = fn (mixed $value): ?int => is_numeric($value) && (int) $value >= 1 && (float) $value == (int) $value
            ? (int) $value
            : null;

        $quantity = $payload['quantity'] ?? null;

        return array_filter([
            'product_id'    => $id($payload['commodity_product_id'] ?? $payload['product_id'] ?? null),
            'quantity'      => is_numeric($quantity) && $quantity > 0 && $quantity <= 10_000_000 ? (float) $quantity : null,
            'unit_id'       => $id($payload['unit_id'] ?? null),
            'unit_label'    => $string($payload['unit_label'] ?? null, 30),
            'delivery_city' => $string($payload['delivery_city'] ?? null, 120),
            'required_by'   => $string($payload['required_by'] ?? null, 120),
            'description'   => $string($payload['description'] ?? null, 2000),
        ], fn ($v) => $v !== null);
    }

    /** @return list<array<string, mixed>>|null  null for an unknown action type */
    private function run(ChatContext $ctx, string $type, array $payload): ?array
    {
        if (in_array($type, self::AUTH_ACTIONS, true) && ! $ctx->authenticated()) {
            return [Blocks::loginRequired($ctx->t('login_required'), $this->resumeAction($type, $payload))];
        }

        return match ($type) {
            'bootstrap'       => $this->menu->bootstrap($ctx),
            'menu'            => $this->menu->menu($ctx),
            'login'           => $this->menu->login($ctx),
            'order.list'      => $this->orders->list($ctx, $payload),
            'order.status'    => $this->orders->status($ctx, $payload),
            'enquiry.list'    => $this->enquiries->list($ctx, $payload),
            'enquiry.status'  => $this->enquiries->status($ctx, $payload),
            'enquiry.start'   => $this->enquiries->start($ctx, $payload),
            'enquiry.submit'  => $this->enquiries->submit($ctx, $payload),
            'product.search'  => $this->products->search($ctx, $payload),
            'lead.start'      => $this->leads->start($ctx),
            'lead.submit'     => $this->leads->submit($ctx, $payload),
            'support.contact' => $this->support->contact($ctx),
            default           => null,
        };
    }
}
