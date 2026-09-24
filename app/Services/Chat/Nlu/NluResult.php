<?php

namespace App\Services\Chat\Nlu;

/**
 * What the assistant understood from one line of free text.
 *
 * Entities: order_ref, enquiry_ref, quantity, unit, product_query,
 * delivery_city, required_by. Absent entities are simply not set.
 */
final class NluResult
{
    public const INTENTS = [
        'greeting',
        'menu',
        'order.status',
        'order.list',
        'enquiry.status',
        'enquiry.list',
        'enquiry.create',
        'product.search',
        'support.contact',
        'credit.info',
        'thanks',
        'fallback',
    ];

    public const ENTITY_KEYS = [
        'order_ref',
        'enquiry_ref',
        'quantity',
        'unit',
        'product_query',
        'delivery_city',
        'required_by',
    ];

    /**
     * Sequence digits are bounded (1-8): a run of digits longer than any real
     * reference is not a reference, so it can never reach a lookup (or its
     * validation) as one.
     */
    public const ORDER_REF_REGEX   = '/\bOID-\d{8}-\d{1,8}\b/i';
    /** `BZN-RFQ-YYMMDD-NNNN`, plus the legacy `PE-YYYYMMDD-NNNN` that older RFQs still carry. */
    public const ENQUIRY_REF_REGEX = '/\b(?:BZN-RFQ-\d{6}|PE-\d{8})-\d{1,8}\b/i';

    /**
     * @param  array<string, mixed>  $entities
     */
    public function __construct(
        public readonly string $intent,
        public readonly float $confidence,
        public readonly array $entities = [],
        public readonly ?string $reply = null,
        public readonly string $engine = 'rules',
    ) {
    }

    public static function fallback(string $engine = 'rules'): self
    {
        return new self('fallback', 0.0, [], null, $engine);
    }

    public function entity(string $key): mixed
    {
        return $this->entities[$key] ?? null;
    }

    /** A reference number was found, which is as certain as the rules get. */
    public function hasReference(): bool
    {
        return ! empty($this->entities['order_ref']) || ! empty($this->entities['enquiry_ref']);
    }

    /** Fills entities this result lacks from another result (never overwrites). */
    public function withEntitiesFrom(self $other): self
    {
        $entities = $this->entities;
        foreach ($other->entities as $key => $value) {
            if (($entities[$key] ?? null) === null && $value !== null) {
                $entities[$key] = $value;
            }
        }

        return new self($this->intent, $this->confidence, $entities, $this->reply, $this->engine);
    }
}
