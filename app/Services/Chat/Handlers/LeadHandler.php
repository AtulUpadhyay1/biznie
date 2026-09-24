<?php

namespace App\Services\Chat\Handlers;

use App\Models\GeneralEnquiry;
use App\Services\Chat\Blocks;
use App\Services\Chat\ChatContext;
use App\Services\Rfq\PushNotifier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * "Request a callback": the same GeneralEnquiry lead the public enquiry form
 * (`POST /api/v2/enquiry`) creates, so it lands in the same admin queue.
 */
class LeadHandler
{
    public function __construct(private readonly MenuHandler $menu)
    {
    }

    public function start(ChatContext $ctx): array
    {
        $prefill = [];
        if ($ctx->user) {
            $prefill = array_filter([
                'name'           => (string) $ctx->user->name,
                'contact_number' => (string) $ctx->user->phone,
                'email'          => (string) $ctx->user->email,
            ], fn (string $v) => $v !== '');
        }

        return [
            Blocks::text($ctx->t('lead_form_intro')),
            Blocks::leadForm($prefill),
        ];
    }

    public function submit(ChatContext $ctx, array $payload): array
    {
        $data = Validator::make($payload, [
            'name'              => ['required', 'string', 'max:120'],
            'contact_number'    => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{6,20}$/'],
            'email'             => ['nullable', 'email', 'max:160'],
            'requirement'       => ['required', 'string', 'max:2000'],
            'delivery_location' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $lead = new GeneralEnquiry();
        $lead->unique_id         = 'ENQ-' . strtoupper(Str::random(8));
        $lead->user_id           = $ctx->user?->id;
        $lead->name              = $data['name'];
        $lead->contact_number    = $data['contact_number'];
        $lead->email             = $data['email'] ?? null;
        $lead->delivery_location = $data['delivery_location'] ?? null;
        $lead->requirement       = $data['requirement'];
        $lead->message           = 'Callback requested via Biznie AI chat';
        $lead->status            = 'pending';
        $lead->save();

        // The bell is a courtesy; a failed notification must not lose the lead.
        // It goes out only once the turn has committed (a failure there is
        // reported by ChatContext::runAfterCommit, never rethrown).
        $title = 'New general enquiry';
        $body  = trim($lead->name . ' requested a callback via chat: ' . Str::limit($lead->requirement, 120));
        $meta  = ['id' => $lead->id, 'unique_id' => $lead->unique_id];
        $ctx->afterCommit(fn () => app(PushNotifier::class)->toAdmins($title, $body, 'general_enquiry', $meta));

        return [
            Blocks::text($ctx->t('lead_created', ['ref' => $lead->unique_id])),
            Blocks::leadCreated($lead->unique_id),
            $this->menu->backToMenu($ctx),
        ];
    }
}
