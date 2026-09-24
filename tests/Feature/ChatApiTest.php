<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\CommodityProduct;
use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiry;
use App\Models\User;
use App\Services\Chat\ChatContext;
use App\Services\Chat\Nlu\ClaudeClassifier;
use App\Services\Chat\Nlu\NluResult;
use App\Services\Chat\Nlu\RuleBasedClassifier;
use App\Models\SellerCommodityProduct;
use App\Services\Chat\ChatLogger;
use App\Services\Chat\ChatReply;
use App\Services\Rfq\PushNotifier;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Psr\Http\Client\ClientInterface;
use Tests\TestCase;

/**
 * POST /api/v2/chat/messages, end to end.
 *
 * `DatabaseTransactions`, not `RefreshDatabase`: there is no separate test
 * database, so the suite runs against the working one and must only roll
 * back. The Claude layer is switched off (or faked) — no test calls the API.
 */
class ChatApiTest extends TestCase
{
    use DatabaseTransactions;

    /** Records every push instead of sending it: the dev DB holds real device tokens. */
    private PushNotifier $pushes;

    protected function setUp(): void
    {
        parent::setUp();

        config(['biznie.chat.llm.enabled' => false]);

        $this->pushes = new class extends PushNotifier {
            /** @var list<array{to: string, title: string}> */
            public array $sent = [];

            public function toUser(User $user, string $title, string $body, string $type = 'notification', array $payload = [], bool $save = false): void
            {
                $this->sent[] = ['to' => 'user:' . $user->id, 'title' => $title];
            }

            public function toAdmins(string $title, string $body, string $type = 'notification', array $payload = []): void
            {
                $this->sent[] = ['to' => 'admins', 'title' => $title];
            }
        };
        $this->app->instance(PushNotifier::class, $this->pushes);
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function makeUser(array $attributes = []): User
    {
        $user = new User();
        $user->name     = 'Chat Tester ' . uniqid();
        $user->type     = 'customer';
        $user->phone    = (string) random_int(7000000000, 9999999999);
        $user->email    = 'chat-' . uniqid() . '@example.test';
        $user->password = bcrypt('secret123');
        $user->status   = 'active';
        foreach ($attributes as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        return $user;
    }

    private function makeOrder(User $customer, string $status = 'pending'): CommodityProductOrder
    {
        $order = new CommodityProductOrder();
        $order->customer_user_id = $customer->id;
        $order->order_id         = 'OID-' . date('Ymd') . '-' . random_int(100000, 999999);
        $order->status           = $status;
        $order->total_amount     = 100000;
        $order->paid_amount      = 10000;
        $order->due_amount       = 90000;
        $order->history          = [['status' => 'Order Confirmed By Customer', 'created_at' => now()->toIso8601String()]];
        $order->save();

        return $order;
    }

    /** A seller with an active listing for `$product`, so a new RFQ fans out to them. */
    private function makeSeller(CommodityProduct $product): User
    {
        $seller = $this->makeUser(['type' => 'seller']);

        $listing = new SellerCommodityProduct();
        $listing->user_id              = $seller->id;
        $listing->commodity_product_id = $product->id;
        $listing->name                 = $product->name;
        $listing->status               = 'active';
        $listing->loading_address      = [['city' => 'Raipur', 'state' => 'Chhattisgarh']];
        $listing->save();

        return $seller;
    }

    /** An RFQ row written directly, e.g. a legacy one from before live bidding. */
    private function makeEnquiry(User $buyer, array $attributes = []): ProductEnquiry
    {
        $enquiry = new ProductEnquiry();
        $enquiry->user_id   = $buyer->id;
        $enquiry->unique_id = 'PE-' . date('Ymd') . '-' . random_int(1000, 9999);
        $enquiry->status    = 'pending';
        foreach ($attributes as $key => $value) {
            $enquiry->{$key} = $value;
        }
        $enquiry->save();

        return $enquiry->fresh();
    }

    private function activeProduct(): CommodityProduct
    {
        $product = CommodityProduct::active()->first();
        if (! $product) {
            $this->markTestSkipped('No active commodity product to raise an RFQ against.');
        }

        return $product;
    }

    private function writeKey(?User $user): string
    {
        return 'chat-write:' . ($user ? 'u:' . $user->id : 'ip:' . app(ChatLogger::class)->hashIp('127.0.0.1'));
    }

    private function send(array $body, ?string $sessionId = null, ?string $clientId = null): TestResponse
    {
        return $this->postJson('/api/v2/chat/messages', array_merge([
            'session_id'        => $sessionId ?? (string) Str::uuid(),
            'client_message_id' => $clientId ?? (string) Str::uuid(),
            'language'          => 'en',
        ], $body));
    }

    private function action(string $type, array $payload = [], ?string $sessionId = null, ?string $clientId = null): TestResponse
    {
        $action = ['type' => $type];
        if ($payload) {
            $action['payload'] = $payload;
        }

        return $this->send(['action' => $action], $sessionId, $clientId);
    }

    /**
     * Binds a ClaudeClassifier whose SDK client talks to a mocked transport
     * returning one canned Messages API response. Nothing leaves the box.
     */
    private function fakeClaude(array $content, string $stopReason, array &$sent): void
    {
        $mock = new MockHandler([new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'id'            => 'msg_test',
            'type'          => 'message',
            'role'          => 'assistant',
            'model'         => 'claude-opus-5',
            'content'       => $content,
            'stop_reason'   => $stopReason,
            'stop_sequence' => null,
            'usage'         => ['input_tokens' => 10, 'output_tokens' => 5],
        ]))]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($sent));
        $transport = new GuzzleClient(['handler' => $stack]);

        $this->app->instance(ClaudeClassifier::class, new class($transport) extends ClaudeClassifier {
            public function __construct(private readonly ClientInterface $transport)
            {
            }

            protected function client(?ClientInterface $transporter = null): \Anthropic\Client
            {
                return parent::client($this->transport);
            }
        });
    }

    /** @return list<array> */
    private function blocks(TestResponse $response): array
    {
        return $response->json('data.messages.0.blocks');
    }

    private function block(TestResponse $response, string $type): ?array
    {
        return collect($this->blocks($response))->firstWhere('type', $type);
    }

    /* ------------------------------------------------------------------ */
    /* Guests                                                              */
    /* ------------------------------------------------------------------ */

    public function test_guest_bootstrap_gets_greeting_and_main_menu(): void
    {
        $response = $this->action('bootstrap');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.authenticated', false)
            ->assertJsonPath('data.engine', 'action')
            ->assertJsonPath('data.intent', 'bootstrap')
            ->assertJsonPath('data.messages.0.role', 'bot');

        $menu = $this->block($response, 'quick_replies');
        $this->assertNotNull($menu);

        $types = collect($menu['options'])->pluck('action.type')->all();
        foreach (['order.list', 'enquiry.list', 'enquiry.start', 'product.search', 'support.contact', 'lead.start', 'login'] as $type) {
            $this->assertContains($type, $types);
        }
    }

    public function test_guest_asking_for_orders_gets_login_required_with_resume_action(): void
    {
        $response = $this->action('order.list', ['scope' => 'active']);

        $response->assertOk()->assertJsonPath('data.authenticated', false);

        $login = $this->block($response, 'login_required');
        $this->assertNotNull($login);
        $this->assertSame('order.list', $login['resume_action']['type']);
        $this->assertSame(['scope' => 'active'], $login['resume_action']['payload']);
    }

    public function test_an_invalid_token_is_treated_as_a_guest_not_a_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer 999999|not-a-real-token')->action('bootstrap');

        $response->assertOk()->assertJsonPath('data.authenticated', false);
    }

    public function test_unknown_action_returns_an_error_block_not_a_500(): void
    {
        $response = $this->action('orders.delete_everything');

        $response->assertOk()->assertJsonPath('data.intent', null);
        $this->assertSame('error', $this->blocks($response)[0]['type']);
    }

    /* ------------------------------------------------------------------ */
    /* Orders                                                              */
    /* ------------------------------------------------------------------ */

    public function test_order_list_shows_only_the_users_own_orders(): void
    {
        $me    = $this->makeUser();
        $other = $this->makeUser();
        $mine  = $this->makeOrder($me);
        $this->makeOrder($other);

        Sanctum::actingAs($me);
        $response = $this->action('order.list', ['scope' => 'all']);

        $response->assertOk()->assertJsonPath('data.authenticated', true);
        $list = $this->block($response, 'order_list');

        $this->assertNotNull($list);
        $this->assertSame([$mine->id], collect($list['orders'])->pluck('id')->all());
        $this->assertSame('/orders/' . $mine->id, $list['orders'][0]['href']);
        $this->assertSame('placed', $list['orders'][0]['stage']);
    }

    public function test_order_status_by_ref_and_foreign_ref_is_not_found(): void
    {
        $me      = $this->makeUser();
        $other   = $this->makeUser();
        $mine    = $this->makeOrder($me, 'Dispatched');
        $foreign = $this->makeOrder($other);

        Sanctum::actingAs($me);

        $found = $this->action('order.status', ['ref' => $mine->order_id]);
        $detail = $this->block($found, 'order_detail');
        $this->assertNotNull($detail);
        $this->assertSame('dispatched', $detail['order']['stage']);
        $this->assertSame('/orders/' . $mine->id . '/ledger', $detail['order']['ledger_href']);
        $this->assertIsArray($detail['order']['tracking']);

        $missing  = $this->action('order.status', ['ref' => 'OID-19990101-0001']);
        $stranger = $this->action('order.status', ['ref' => $foreign->order_id]);

        $this->assertNull($this->block($stranger, 'order_detail'));
        // A foreign reference must be indistinguishable from a missing one.
        $this->assertSame($this->blocks($missing), $this->blocks($stranger));
    }

    public function test_staff_sub_user_sees_the_owners_orders(): void
    {
        $owner = $this->makeUser();
        $staff = $this->makeUser(['is_staff' => 1, 'added_by' => $owner->id]);
        $order = $this->makeOrder($owner);

        Sanctum::actingAs($staff);

        $chat = $this->block($this->action('order.list', ['scope' => 'all']), 'order_list');
        $this->assertSame([$order->id], collect($chat['orders'])->pluck('id')->all());

        // Same fix on the dashboard API (plan §5.6).
        $this->getJson('/api/v2/me/orders')->assertOk()->assertJsonPath('data.0.id', $order->id);
        $this->getJson('/api/v2/me/orders/' . $order->id)->assertOk()->assertJsonPath('data.tracking', []);
    }

    /* ------------------------------------------------------------------ */
    /* Enquiries                                                           */
    /* ------------------------------------------------------------------ */

    public function test_enquiry_submit_creates_an_rfq_with_bidding_open_and_replay_is_idempotent(): void
    {
        $product = $this->activeProduct();
        $seller  = $this->makeSeller($product);

        $buyer = $this->makeUser();
        Sanctum::actingAs($buyer);
        $before = ProductEnquiry::where('user_id', $buyer->id)->count();

        $sessionId = (string) Str::uuid();
        $clientId  = (string) Str::uuid();
        $payload   = [
            'commodity_product_id' => $product->id,
            'quantity'             => 25,
            'unit_label'           => 'MT',
            'delivery_city'        => 'Pune',
            'required_by'          => 'Within 1 week',
            'description'          => 'Chat test',
        ];

        $first = $this->action('enquiry.submit', $payload, $sessionId, $clientId);
        $first->assertOk()->assertJsonPath('data.intent', 'enquiry.submit');

        $created = $this->block($first, 'enquiry_created');
        $this->assertNotNull($created);
        $this->assertMatchesRegularExpression(NluResult::ENQUIRY_REF_REGEX, $created['enquiry']['ref']);

        $rfq = ProductEnquiry::find($created['enquiry']['id']);
        $this->assertSame($buyer->id, (int) $rfq->user_id);
        $this->assertSame('live', $rfq->bidding_status);
        $this->assertEquals(25.0, (float) $rfq->quantity);
        $this->assertSame('Pune', $rfq->delivery_city);
        $this->assertSame($before + 1, ProductEnquiry::where('user_id', $buyer->id)->count());

        // Fanned out once the turn committed: our seller was pushed exactly once.
        $toSeller = fn () => collect($this->pushes->sent)->where('to', 'user:' . $seller->id)->count();
        $this->assertSame(1, $toSeller());
        $pushes = count($this->pushes->sent);

        // Same (session, client_message_id): stored reply, nothing re-runs.
        $replay = $this->action('enquiry.submit', $payload, $sessionId, $clientId);
        $replay->assertOk();
        $this->assertSame($first->json('data.messages.0.id'), $replay->json('data.messages.0.id'));
        $this->assertSame($before + 1, ProductEnquiry::where('user_id', $buyer->id)->count());
        $this->assertCount($pushes, $this->pushes->sent);
        $this->assertSame(1, $toSeller());
    }

    public function test_a_rolled_back_turn_creates_no_rfq_pushes_nothing_and_refunds_the_write(): void
    {
        $product = $this->activeProduct();
        $this->makeSeller($product);
        $buyer = $this->makeUser();
        Sanctum::actingAs($buyer);

        // Everything up to the stored reply works; storing it fails.
        $logger = new class extends ChatLogger {
            public ?array $blocks = null;

            public function logBotTurn(ChatMessage $userTurn, ChatReply $reply, int $latencyMs): ChatMessage
            {
                $this->blocks = $reply->blocks;

                throw new \RuntimeException('disk full');
            }
        };
        $this->app->instance(ChatLogger::class, $logger);

        $sessionId = (string) Str::uuid();
        $clientId  = (string) Str::uuid();

        $this->action('enquiry.submit', [
            'commodity_product_id' => $product->id,
            'quantity'             => 5,
            'unit_label'           => 'MT',
            'delivery_city'        => 'Pune',
            'required_by'          => 'Within 1 week',
        ], $sessionId, $clientId)->assertStatus(500);

        // The RFQ really was raised inside the turn...
        $this->assertNotNull(collect($logger->blocks)->firstWhere('type', 'enquiry_created'));
        // ...and rolled back with it, before any seller heard about it.
        $this->assertSame(0, ProductEnquiry::where('user_id', $buyer->id)->count());
        $this->assertSame([], $this->pushes->sent);
        $this->assertSame(0, (int) RateLimiter::attempts($this->writeKey($buyer)));
        // The claim rolled back with it, so the same send can be retried.
        $this->assertFalse(ChatMessage::where('client_message_id', $clientId)->exists());
    }

    public function test_enquiry_submit_accepts_a_free_text_unit_without_unit_id(): void
    {
        $product = $this->activeProduct();
        $buyer   = $this->makeUser();
        Sanctum::actingAs($buyer);

        $base = [
            'commodity_product_id' => $product->id,
            'quantity'             => 12,
            'delivery_city'        => 'Nagpur',
            'required_by'          => 'Within 3 days',
        ];

        $created = $this->block(
            $this->action('enquiry.submit', $base + ['unit_id' => null, 'unit_label' => 'Truckloads'])->assertOk(),
            'enquiry_created'
        );
        $this->assertNotNull($created);
        $rfq = ProductEnquiry::find($created['enquiry']['id']);
        $this->assertNull($rfq->unit_id);
        $this->assertSame('Truckloads', $rfq->unit_label);

        // Neither a unit id nor a label: 422 on unit_id.
        $this->action('enquiry.submit', $base + ['unit_id' => null, 'unit_label' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['unit_id']);
    }

    public function test_guest_enquiry_submit_resumes_as_a_prefilled_form_not_a_submit(): void
    {
        $product = $this->activeProduct();

        $response = $this->action('enquiry.submit', [
            'commodity_product_id' => $product->id,
            'quantity'             => 25,
            'unit_id'              => null,
            'unit_label'           => 'MT',
            'delivery_city'        => 'Pune',
            'required_by'          => 'Within 1 week',
            'description'          => 'Fe 500D',
        ]);

        $response->assertOk()->assertJsonPath('data.authenticated', false);
        $login = $this->block($response, 'login_required');
        $this->assertSame('enquiry.start', $login['resume_action']['type']);
        $this->assertEquals([
            'product_id'    => $product->id,
            'quantity'      => 25,
            'unit_label'    => 'MT',
            'delivery_city' => 'Pune',
            'required_by'   => 'Within 1 week',
            'description'   => 'Fe 500D',
        ], $login['resume_action']['payload']);

        // A guest's submit is not a write: it did not touch the cap.
        $this->assertSame(0, (int) RateLimiter::attempts($this->writeKey(null)));

        // After login, the resume opens the form pre-filled — nothing is created.
        $buyer = $this->makeUser();
        Sanctum::actingAs($buyer);
        $resumed = $this->action('enquiry.start', $login['resume_action']['payload'])->assertOk();
        $form    = $this->block($resumed, 'enquiry_form');
        $this->assertSame($product->id, $form['prefill']['product']['id']);
        $this->assertEquals(25, $form['prefill']['quantity']);
        $this->assertSame('Fe 500D', $form['prefill']['description']);
        $this->assertSame(0, ProductEnquiry::where('user_id', $buyer->id)->count());
    }

    public function test_legacy_enquiries_get_a_stage_from_their_status_not_closed(): void
    {
        $buyer = $this->makeUser();
        Sanctum::actingAs($buyer);

        $sent     = $this->makeEnquiry($buyer, ['status' => 'Enquiry Sent To Seller']);
        $replied  = $this->makeEnquiry($buyer, ['status' => 'Seller Replied']);
        $marked   = $this->makeEnquiry($buyer, ['status' => 'Seller Marked']);
        $phone    = $this->makeEnquiry($buyer, ['status' => 'Process Over Phone']);
        $ordered  = $this->makeEnquiry($buyer, ['status' => 'ordered']);
        $cancel   = $this->makeEnquiry($buyer, ['status' => 'Cancelled By Customer']);
        // Went through live bidding and lapsed with no offer: genuinely closed.
        $lapsed   = $this->makeEnquiry($buyer, [
            'status'             => 'Enquiry Sent To Seller',
            'bidding_status'     => 'closed',
            'bidding_started_at' => now()->subHour(),
            'bidding_ends_at'    => now()->subMinutes(45),
        ]);

        $all = collect($this->block($this->action('enquiry.list', ['scope' => 'all', 'page' => 1]), 'enquiry_list')['enquiries'])
            ->merge($this->block($this->action('enquiry.list', ['scope' => 'all', 'page' => 2]), 'enquiry_list')['enquiries'] ?? [])
            ->pluck('stage', 'id');

        $this->assertSame('sent', $all[$sent->id]);
        $this->assertSame('offer_ready', $all[$replied->id]);
        $this->assertSame('offer_ready', $all[$marked->id]);
        $this->assertSame('offer_ready', $all[$phone->id]);
        $this->assertSame('ordered', $all[$ordered->id]);
        $this->assertSame('cancelled', $all[$cancel->id]);
        $this->assertSame('closed', $all[$lapsed->id]);

        // "Open" means exactly: not ordered, cancelled or closed.
        $open = collect($this->block($this->action('enquiry.list', ['scope' => 'open']), 'enquiry_list')['enquiries'])
            ->pluck('id')->sort()->values()->all();
        $expected = collect([$sent->id, $replied->id, $marked->id, $phone->id])->sort()->values()->all();
        $this->assertSame($expected, $open);

        // The detail says what actually happens next.
        $detail = $this->block($this->action('enquiry.status', ['id' => $marked->id]), 'enquiry_detail')['enquiry'];
        $this->assertSame('offer_ready', $detail['stage']);
        $this->assertSame(__('chat.enquiry_next_step.team_contact', [], 'en'), $detail['next_step']);

        $detail = $this->block($this->action('enquiry.status', ['id' => $sent->id]), 'enquiry_detail')['enquiry'];
        $this->assertSame(__('chat.enquiry_next_step.awaiting_offers', [], 'en'), $detail['next_step']);
    }

    public function test_enquiry_submit_validation_errors_return_422(): void
    {
        Sanctum::actingAs($this->makeUser());

        $this->action('enquiry.submit', ['quantity' => 0, 'required_by' => 'Someday'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['commodity_product_id', 'quantity', 'delivery_city', 'required_by']);
    }

    public function test_enquiry_start_prefills_the_form_from_free_text(): void
    {
        Sanctum::actingAs($this->makeUser());

        $response = $this->send(['message' => 'need 20 MT TMT in Pune within a week']);

        $response->assertOk()
            ->assertJsonPath('data.intent', 'enquiry.create')
            ->assertJsonPath('data.engine', 'rules');

        $form = $this->block($response, 'enquiry_form');
        $this->assertNotNull($form);
        $this->assertEquals(20, $form['prefill']['quantity']);
        $this->assertSame('Pune', $form['prefill']['delivery_city']);
        $this->assertSame('Within 1 week', $form['prefill']['required_by']);
        $this->assertSame(config('biznie.chat.required_by_options'), $form['required_by_options']);
        $this->assertIsArray($form['units']);
    }

    /* ------------------------------------------------------------------ */
    /* NLU                                                                 */
    /* ------------------------------------------------------------------ */

    public function test_rule_nlu_resolves_order_status_with_ref_in_english_and_hinglish(): void
    {
        $rules = new RuleBasedClassifier();

        foreach ([
            'where is my order OID-20260901-1234',
            'mera order OID-20260901-1234 kahan hai?',
            'OID-20260901-1234 kab aayega',
        ] as $text) {
            $result = $rules->analyse($text);
            $this->assertSame('order.status', $result->intent, $text);
            $this->assertSame('OID-20260901-1234', $result->entity('order_ref'), $text);
        }

        $this->assertSame('order.status', $rules->analyse('mera order kab aayega')->intent);
        $this->assertSame('order.status', $rules->analyse('मेरा ऑर्डर कहाँ है')->intent);
        $this->assertSame('enquiry.status', $rules->analyse('status of BZN-RFQ-260901-0007')->intent);
        // Older RFQs still carry the legacy PE- reference.
        $legacy = $rules->analyse('pe-20260514-5939 ka status');
        $this->assertSame('enquiry.status', $legacy->intent);
        $this->assertSame('PE-20260514-5939', $legacy->entity('enquiry_ref'));
        $this->assertSame('product.search', $rules->analyse('aaj tmt ka bhav kya hai')->intent);
        $this->assertSame('enquiry.create', $rules->analyse('need 20 mt tmt in pune')->intent);
    }

    public function test_rule_nlu_keeps_thousands_separators(): void
    {
        $rules = new RuleBasedClassifier();

        $wire = $rules->analyse('need 1,000 kg binding wire');
        $this->assertSame('enquiry.create', $wire->intent);
        $this->assertSame(1000.0, $wire->entity('quantity'));
        $this->assertSame('Kg', $wire->entity('unit'));
        $this->assertSame('binding wire', $wire->entity('product_query'));

        // Indian grouping too.
        $this->assertSame(150000.0, $rules->analyse('1,50,000 kg scrap chahiye')->entity('quantity'));
        // A list of sizes is not a number.
        $this->assertNull($rules->analyse('TMT 8,10,12 mm')->entity('quantity'));
    }

    public function test_rule_nlu_weak_create_words_alone_are_not_confident(): void
    {
        $rules = new RuleBasedClassifier();

        $help = $rules->analyse('I need help');
        $this->assertSame('support.contact', $help->intent);
        $this->assertLessThan(RuleBasedClassifier::CONFIDENT, $help->confidence);

        $this->assertLessThan(RuleBasedClassifier::CONFIDENT, $rules->analyse('I want something')->confidence);
        $this->assertSame('support.contact', $rules->analyse('I want to talk to someone')->intent);

        // A product or a quantity still makes it a confident requirement.
        $this->assertGreaterThanOrEqual(RuleBasedClassifier::CONFIDENT, $rules->analyse('need tmt bars')->confidence);
        $this->assertSame('enquiry.create', $rules->analyse('20 MT TMT chahiye')->intent);
    }

    public function test_rule_nlu_only_reads_a_city_after_a_delivery_cue(): void
    {
        $rules = new RuleBasedClassifier();

        $this->assertNull($rules->analyse('talk to Sales')->entity('delivery_city'));
        $this->assertNull($rules->analyse('need 20 MT TMT in March')->entity('delivery_city'));
        $this->assertNull($rules->analyse('need TMT, deliver on Monday at Site')->entity('delivery_city'));
        $this->assertNull($rules->analyse('deliver in March')->entity('delivery_city'));

        $this->assertSame('Pune', $rules->analyse('20 MT TMT pune me chahiye')->entity('delivery_city'));
        $this->assertSame('Mumbai', $rules->analyse('deliver to Mumbai')->entity('delivery_city'));
        $this->assertSame('Sangli', $rules->analyse('need 5 MT TMT, deliver to Sangli')->entity('delivery_city'));
        $this->assertSame('Sangli', $rules->analyse('5 MT TMT Sangli me chahiye')->entity('delivery_city'));
    }

    public function test_an_overlong_reference_in_free_text_is_ignored_not_a_422(): void
    {
        $rules = new RuleBasedClassifier();
        $this->assertNull($rules->analyse('where is my order OID-20260901-123456789012345678901234567890')->entity('order_ref'));
        $this->assertNull($rules->analyse('status of BZN-RFQ-260901-123456789012345678901234567890')->entity('enquiry_ref'));
        $this->assertSame('OID-20260901-12345678', $rules->analyse('order OID-20260901-12345678')->entity('order_ref'));

        Sanctum::actingAs($this->makeUser());

        $this->send(['message' => 'where is my order OID-20260901-' . str_repeat('9', 60)])
            ->assertOk()
            ->assertJsonPath('data.intent', 'order.status');
        $this->send(['message' => 'status of BZN-RFQ-260901-' . str_repeat('7', 60)])
            ->assertOk()
            ->assertJsonPath('data.intent', 'enquiry.status');
    }

    public function test_free_text_order_status_goes_through_the_rules_when_llm_is_disabled(): void
    {
        $me    = $this->makeUser();
        $order = $this->makeOrder($me);
        Sanctum::actingAs($me);

        $response = $this->send(['message' => 'where is my order ' . $order->order_id]);

        $response->assertOk()
            ->assertJsonPath('data.intent', 'order.status')
            ->assertJsonPath('data.engine', 'rules');
        $this->assertSame($order->id, $this->block($response, 'order_detail')['order']['id']);

        // Nonsense still gets an answer (the fallback plus the menu).
        $fallback = $this->send(['message' => 'qwxz plorp']);
        $fallback->assertOk()->assertJsonPath('data.intent', 'fallback')->assertJsonPath('data.engine', 'rules');
        $this->assertNotNull($this->block($fallback, 'quick_replies'));
    }

    public function test_guest_free_text_for_an_order_gets_login_with_the_resolved_action(): void
    {
        $response = $this->send(['message' => 'mera order OID-20260901-1234 kahan hai']);

        $login = $this->block($response, 'login_required');
        $this->assertSame('order.status', $login['resume_action']['type']);
        $this->assertSame('OID-20260901-1234', $login['resume_action']['payload']['ref']);
    }

    public function test_llm_failure_falls_back_to_rules(): void
    {
        config(['biznie.chat.llm.enabled' => true, 'biznie.chat.llm.api_key' => 'test-key']);

        // A classifier whose transport always fails, so the real API is never hit.
        $this->app->instance(ClaudeClassifier::class, new class extends ClaudeClassifier {
            protected function send(array $messages): object
            {
                throw new \RuntimeException('network down');
            }
        });

        // Low-confidence for the rules ("orders" alone), so the LLM is consulted.
        Sanctum::actingAs($this->makeUser());
        $response = $this->send(['message' => 'orders']);

        $response->assertOk()->assertJsonPath('data.engine', 'rules')->assertJsonPath('data.intent', 'order.list');
    }

    public function test_llm_request_goes_through_the_sdk_with_structured_output(): void
    {
        if (! class_exists(\Anthropic\Client::class)) {
            $this->markTestSkipped('anthropic-ai/sdk is not installed.');
        }

        config([
            'biznie.chat.llm.enabled'         => true,
            'biznie.chat.llm.api_key'         => 'test-key',
            'biznie.chat.llm.server_fallback' => true,
        ]);

        $sent = [];
        $this->fakeClaude([
            ['type' => 'thinking', 'thinking' => '', 'signature' => 'sig'],
            ['type' => 'text', 'text' => json_encode([
                'intent'     => 'credit.info',
                'confidence' => 0.9,
                'entities'   => array_fill_keys(NluResult::ENTITY_KEYS, null),
                'reply'      => 'Credit is available for eligible businesses.',
            ])],
        ], 'end_turn', $sent);

        Sanctum::actingAs($this->makeUser());
        // Weak for the rules ("help" alone), so the LLM is asked.
        $response = $this->send(['message' => 'can you help me with something for my shop']);

        $response->assertOk()->assertJsonPath('data.engine', 'llm')->assertJsonPath('data.intent', 'credit.info');
        $this->assertSame('Credit is available for eligible businesses.', $this->blocks($response)[0]['text']);

        $this->assertCount(1, $sent);
        $request = $sent[0]['request'];
        $body    = json_decode((string) $request->getBody(), true);

        $this->assertStringContainsString('server-side-fallback-2026-07-01', $request->getHeaderLine('anthropic-beta'));
        $this->assertSame('claude-opus-5', $body['model']);
        $this->assertSame(2048, $body['max_tokens']);
        $this->assertSame('default', $body['fallbacks']);
        $this->assertSame('low', $body['output_config']['effort']);
        $this->assertSame('json_schema', $body['output_config']['format']['type']);
        $this->assertSame('ephemeral', $body['system'][0]['cache_control']['type']);
        $this->assertArrayNotHasKey('temperature', $body);
        $this->assertArrayNotHasKey('thinking', $body);
        $this->assertSame('user', $body['messages'][0]['role']);
    }

    public function test_llm_refusal_falls_back_to_rules(): void
    {
        if (! class_exists(\Anthropic\Client::class)) {
            $this->markTestSkipped('anthropic-ai/sdk is not installed.');
        }

        config(['biznie.chat.llm.enabled' => true, 'biznie.chat.llm.api_key' => 'test-key']);

        $sent = [];
        $this->fakeClaude([['type' => 'text', 'text' => '']], 'refusal', $sent);

        Sanctum::actingAs($this->makeUser());
        $response = $this->send(['message' => 'orders']);

        $response->assertOk()->assertJsonPath('data.engine', 'rules')->assertJsonPath('data.intent', 'order.list');
        $this->assertCount(1, $sent);
    }

    public function test_llm_output_is_revalidated(): void
    {
        $claude = new ClaudeClassifier();

        $result = $claude->validate([
            'intent'     => 'order.status',
            'confidence' => 7,
            'entities'   => [
                'order_ref'     => 'OID-20260101-9999', // not in the user's text
                'enquiry_ref'   => null,
                'quantity'      => -3,
                'unit'          => null,
                'product_query' => '<b>tmt</b>',
                'delivery_city' => 'Pune; DROP TABLE',
                'required_by'   => 'tomorrow',
            ],
            'reply'      => 'ignored for this intent',
        ], 'where is my order');

        $this->assertSame('order.status', $result->intent);
        $this->assertSame(1.0, $result->confidence);
        $this->assertSame(['product_query' => 'tmt'], $result->entities);
        $this->assertNull($result->reply);
        $this->assertSame('llm', $result->engine);

        $this->assertNull($claude->validate(['intent' => 'order.cancel'], 'cancel my order'));
    }

    /* ------------------------------------------------------------------ */
    /* Contract, validation, sessions                                      */
    /* ------------------------------------------------------------------ */

    public function test_request_validation_returns_422(): void
    {
        $this->postJson('/api/v2/chat/messages', ['message' => 'hi'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['session_id', 'client_message_id']);

        $this->send(['message' => 'hi', 'action' => ['type' => 'menu']])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        $this->send([])->assertStatus(422)->assertJsonValidationErrors(['message']);

        $this->send(['message' => str_repeat('a', 1001)])->assertStatus(422)->assertJsonValidationErrors(['message']);

        $this->send(['message' => 'hi', 'language' => 'fr'])->assertStatus(422)->assertJsonValidationErrors(['language']);
    }

    public function test_rate_limits_return_429_with_retry_after(): void
    {
        $sessionId = (string) Str::uuid();
        for ($i = 0; $i < 15; $i++) {
            $this->action('menu', [], $sessionId)->assertOk();
        }

        $this->action('menu', [], $sessionId)
            ->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['message', 'retry_after']);
    }

    public function test_writes_are_capped_per_hour(): void
    {
        Sanctum::actingAs($user = $this->makeUser());

        $key = 'chat-write:u:' . $user->id;
        for ($i = 0; $i < 10; $i++) {
            RateLimiter::hit($key, 3600);
        }

        $this->action('lead.submit', ['name' => 'A', 'contact_number' => '9876543210', 'requirement' => 'x'])
            ->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['message', 'retry_after']);

        // A rejected send does not eat into the next window either.
        $this->assertSame(10, (int) RateLimiter::attempts($key));
    }

    public function test_a_write_that_fails_validation_gives_its_slot_back(): void
    {
        Sanctum::actingAs($user = $this->makeUser());

        $key = $this->writeKey($user);
        for ($i = 0; $i < 9; $i++) {
            RateLimiter::hit($key, 3600);
        }

        // Taken up front, handed back on the 422.
        $this->action('enquiry.submit', ['quantity' => 0])->assertStatus(422);
        $this->action('lead.submit', ['name' => 'A'])->assertStatus(422);
        $this->assertSame(9, (int) RateLimiter::attempts($key));

        // So the 10th real write still goes through, and the 11th does not.
        $lead = ['name' => 'A', 'contact_number' => '9876543210', 'requirement' => 'x'];
        $this->action('lead.submit', $lead)->assertOk();
        $this->assertSame(10, (int) RateLimiter::attempts($key));
        $this->action('lead.submit', $lead)->assertStatus(429);
    }

    public function test_a_session_is_never_shared_between_users(): void
    {
        $alice = $this->makeUser();
        $bob   = $this->makeUser();
        $sessionId = (string) Str::uuid();

        Sanctum::actingAs($alice);
        $this->action('bootstrap', [], $sessionId)->assertJsonPath('data.session_id', $sessionId);

        Sanctum::actingAs($bob);
        $other = $this->action('bootstrap', [], $sessionId);
        $this->assertNotSame($sessionId, $other->json('data.session_id'));
        $this->assertSame($bob->id, ChatSession::find($other->json('data.session_id'))->user_id);
    }

    public function test_a_retried_write_on_a_foreign_session_is_replayed_not_rerun(): void
    {
        $alice     = $this->makeUser();
        $bob       = $this->makeUser();
        $sessionId = (string) Str::uuid();
        $clientId  = (string) Str::uuid();

        Sanctum::actingAs($alice);
        $this->action('bootstrap', [], $sessionId)->assertOk();

        // Bob (e.g. a shared device) writes into Alice's session id, then retries.
        Sanctum::actingAs($bob);
        $lead   = ['name' => 'Bob', 'contact_number' => '9876543210', 'requirement' => '10 MT TMT'];
        $first  = $this->action('lead.submit', $lead, $sessionId, $clientId)->assertOk();
        $replay = $this->action('lead.submit', $lead, $sessionId, $clientId)->assertOk();

        $replacement = $first->json('data.session_id');
        $this->assertNotSame($sessionId, $replacement);
        $this->assertSame($replacement, $replay->json('data.session_id'));
        $this->assertSame($first->json('data.messages.0.id'), $replay->json('data.messages.0.id'));
        $this->assertSame(1, ChatMessage::where('chat_session_id', $replacement)->where('role', 'user')->count());
        $this->assertCount(1, collect($this->pushes->sent)->where('to', 'admins'));
        $this->assertSame(1, (int) RateLimiter::attempts($this->writeKey($bob)));

        // Deterministic per caller: a guest on the same id gets a different one.
        $logger = app(ChatLogger::class);
        $this->assertSame($replacement, $logger->replacementId($sessionId, $bob->id));
        $this->assertNotSame($replacement, $logger->replacementId($sessionId, null));
        $this->assertNotSame($replacement, $logger->replacementId($sessionId, $alice->id));
    }

    public function test_invalid_utf8_is_a_422_not_a_500(): void
    {
        $headers = ['HTTP_ACCEPT' => 'application/json'];
        $base    = ['session_id' => (string) Str::uuid(), 'client_message_id' => (string) Str::uuid(), 'language' => 'en'];

        $this->call('POST', '/api/v2/chat/messages', $base + ['message' => "need \xC3\x28 TMT"], [], [], $headers)
            ->assertStatus(422)
            ->assertJsonPath('errors.message.0', __('chat.validation_encoding', [], 'en'));

        $this->call('POST', '/api/v2/chat/messages', $base + [
            'action' => ['type' => 'lead.submit', 'payload' => ['name' => "Ra\xFFvi", 'contact_number' => '9876543210', 'requirement' => 'x']],
        ], [], [], $headers)
            ->assertStatus(422)
            ->assertJsonPath('errors.action.0', __('chat.validation_encoding', [], 'en'));

        $this->assertFalse(ChatSession::whereKey($base['session_id'])->exists());
    }

    public function test_turns_are_logged_without_lead_pii(): void
    {
        $sessionId = (string) Str::uuid();

        $response = $this->action('lead.submit', [
            'name'           => 'Ravi Kumar',
            'contact_number' => '9876543210',
            'requirement'    => '50 MT TMT bars',
        ], $sessionId);

        $response->assertOk();
        $this->assertMatchesRegularExpression('/^ENQ-[A-Z0-9]{8}$/', $this->block($response, 'lead_created')['reference']);

        $userTurn = ChatMessage::where('chat_session_id', $sessionId)->where('role', 'user')->first();
        $this->assertSame('lead.submit', $userTurn->action->type);
        $this->assertFalse(isset($userTurn->action->payload));

        $session = ChatSession::find($sessionId);
        $this->assertSame(64, strlen($session->ip_hash));
        $this->assertNotSame('127.0.0.1', $session->ip_hash);
    }

    public function test_language_selects_localised_copy(): void
    {
        $response = $this->send(['action' => ['type' => 'bootstrap'], 'language' => 'hi']);

        $this->assertStringContainsString('Namaste', $this->blocks($response)[0]['text']);
        $this->assertSame(
            __('chat.menu.track_order', [], 'hi'),
            $this->block($response, 'quick_replies')['options'][0]['label']
        );
    }

    public function test_context_owner_id_uses_added_by_for_staff(): void
    {
        $owner = $this->makeUser();
        $staff = $this->makeUser(['is_staff' => 1, 'added_by' => $owner->id]);
        $session = new ChatSession(['id' => (string) Str::uuid()]);

        $this->assertSame($owner->id, (new ChatContext($staff, 'en', $session, 'x'))->ownerId());
        $this->assertNull((new ChatContext(null, 'en', $session, 'x'))->ownerId());
    }
}
