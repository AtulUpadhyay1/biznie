<?php

namespace App\Services\Chat;

/**
 * Builders for the typed blocks in the chat contract (plan §4.3).
 *
 * Text is always plain text; the client never renders HTML. Optional objects
 * that may be empty are cast to `object` so they encode as `{}`, not `[]`.
 */
final class Blocks
{
    public const ENGINE_RULES  = 'rules';
    public const ENGINE_LLM    = 'llm';
    public const ENGINE_ACTION = 'action';

    public static function text(string $text): array
    {
        return ['type' => 'text', 'text' => $text];
    }

    /** A `ChatAction`; `payload` is omitted when empty so it never encodes as `[]`. */
    public static function action(string $type, array $payload = []): array
    {
        $action = ['type' => $type];
        if ($payload !== []) {
            $action['payload'] = $payload;
        }

        return $action;
    }

    public static function option(string $label, ?array $action = null, ?string $message = null): array
    {
        $option = ['label' => $label];
        if ($action !== null) {
            $option['action'] = $action;
        }
        if ($message !== null) {
            $option['message'] = $message;
        }

        return $option;
    }

    /** @param  list<array>  $options */
    public static function quickReplies(array $options): array
    {
        return ['type' => 'quick_replies', 'options' => array_values($options)];
    }

    public static function loginRequired(string $text, ?array $resumeAction = null): array
    {
        $block = ['type' => 'login_required', 'text' => $text];
        if ($resumeAction !== null) {
            $block['resume_action'] = $resumeAction;
        }

        return $block;
    }

    public static function orderList(array $orders, bool $hasMore): array
    {
        return [
            'type'          => 'order_list',
            'orders'        => array_values($orders),
            'has_more'      => $hasMore,
            'view_all_href' => '/orders',
        ];
    }

    public static function orderDetail(array $order): array
    {
        return ['type' => 'order_detail', 'order' => $order];
    }

    public static function enquiryList(array $enquiries, bool $hasMore): array
    {
        return [
            'type'          => 'enquiry_list',
            'enquiries'     => array_values($enquiries),
            'has_more'      => $hasMore,
            'view_all_href' => '/rfq',
        ];
    }

    public static function enquiryDetail(array $enquiry): array
    {
        return ['type' => 'enquiry_detail', 'enquiry' => $enquiry];
    }

    /**
     * @param  array<string, mixed>  $prefill
     * @param  list<array{id: int, name: string, short_name: ?string}>  $units
     * @param  list<string>  $requiredByOptions
     */
    public static function enquiryForm(array $prefill, array $units, array $requiredByOptions): array
    {
        return [
            'type'                => 'enquiry_form',
            'prefill'             => (object) $prefill,
            'units'               => array_values($units),
            'required_by_options' => array_values($requiredByOptions),
        ];
    }

    public static function enquiryCreated(array $enquiry): array
    {
        return ['type' => 'enquiry_created', 'enquiry' => $enquiry];
    }

    public static function productList(array $products): array
    {
        return ['type' => 'product_list', 'products' => array_values($products)];
    }

    /** @param  array{name?: string, contact_number?: string, email?: string}  $prefill */
    public static function leadForm(array $prefill): array
    {
        return ['type' => 'lead_form', 'prefill' => (object) $prefill];
    }

    public static function leadCreated(string $reference): array
    {
        return ['type' => 'lead_created', 'reference' => $reference];
    }

    public static function contact(?string $phone, ?string $whatsapp, ?string $email): array
    {
        return ['type' => 'contact', 'phone' => $phone, 'whatsapp' => $whatsapp, 'email' => $email];
    }

    public static function error(string $text, bool $retryable = false): array
    {
        return ['type' => 'error', 'text' => $text, 'retryable' => $retryable];
    }
}
