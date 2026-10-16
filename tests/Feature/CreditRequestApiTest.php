<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\CreditWalletDocumentType;
use App\Models\CreditWalletRequest;
use App\Models\ImageUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Credit requests (B30): amount + tenure are captured, documents arrive as
 * files, and a buyer cannot stack a second request on an open one.
 *
 * `DatabaseTransactions` against the working database, as in the other
 * feature tests — there is no separate test database to migrate.
 */
class CreditRequestApiTest extends TestCase
{
    use DatabaseTransactions;

    private CreditWalletDocumentType $type;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        // The admin alert pushes to every admin device: none may leave a test run.
        Admin::query()->update(['fcm_token' => null]);

        $type = new CreditWalletDocumentType();
        $type->name   = 'Test facility ' . uniqid();
        $type->status = 1;
        $type->forms  = [
            ['label' => 'Bank statement', 'type' => 'file', 'required' => 'true', 'description' => 'Last 12 months'],
            ['label' => 'GST Number', 'type' => 'text', 'required' => 'true', 'description' => null],
            ['label' => 'Debt profile', 'type' => 'text', 'required' => 'false', 'description' => null],
        ];
        $type->save();

        $this->type = $type;
    }

    private function makeBuyer(): User
    {
        $user = new User();
        $user->name     = 'Test buyer ' . uniqid();
        $user->type     = 'buyer';
        $user->phone    = (string) random_int(7000000000, 9999999999);
        $user->email    = 'test-' . uniqid() . '@example.test';
        $user->password = bcrypt('secret123');
        $user->status   = 'active';
        $user->save();

        return $user;
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'credit_wallet_document_type_id' => $this->type->id,
            'amount'                         => 2500000,
            'tenure_days'                    => 60,
            'fields'                         => [1 => '09AAACH7409R1ZZ'],
            'files'                          => [0 => UploadedFile::fake()->create('statement.pdf', 120, 'application/pdf')],
        ], $overrides);
    }

    public function test_document_types_expose_name_and_normalised_fields(): void
    {
        Sanctum::actingAs($this->makeBuyer());

        $row = collect($this->getJson('/api/v2/me/credit-document-types')->assertOk()->json('data'))
            ->firstWhere('id', $this->type->id);

        $this->assertSame($this->type->name, $row['name']);
        $this->assertSame(['file', 'text', 'text'], array_column($row['fields'], 'type'));
        $this->assertSame([true, true, false], array_column($row['fields'], 'required'));
    }

    public function test_request_stores_amount_tenure_and_uploaded_documents(): void
    {
        Sanctum::actingAs($buyer = $this->makeBuyer());

        $response = $this->post('/api/v2/me/credit-requests', $this->payload(), ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.status_code', 'pending')
            ->assertJsonPath('data.amount', 2500000)
            ->assertJsonPath('data.tenure_days', 60);

        $submitted = $response->json('data.submitted');
        $this->assertNotNull($submitted[0]['url'], 'the bank statement should come back as a link');
        $this->assertSame('09AAACH7409R1ZZ', $submitted[1]['value']);
        $this->assertNull($submitted[2]['value']);

        $stored = CreditWalletRequest::where('user_id', $buyer->id)->firstOrFail();
        // Stored as an ImageUpload id, the same as the admin screen writes.
        Storage::disk('public')->assertExists(ImageUpload::findOrFail($stored->form_data[0]['value'])->image);
        $this->assertSame('Pending', $stored->status);
    }

    public function test_minimum_amount_and_required_documents_are_enforced(): void
    {
        Sanctum::actingAs($this->makeBuyer());

        $this->post('/api/v2/me/credit-requests', $this->payload(['amount' => 50000]), ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonPath('errors.amount.0', 'Minimum credit limit you can request is ₹1,00,000');

        $errors = $this->post('/api/v2/me/credit-requests', $this->payload(['files' => []]), ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->json('errors');
        $this->assertSame('Bank statement is required', $errors['files.0'][0] ?? null);
    }

    public function test_second_request_is_refused_while_one_is_pending(): void
    {
        Sanctum::actingAs($this->makeBuyer());

        $this->post('/api/v2/me/credit-requests', $this->payload(), ['Accept' => 'application/json'])->assertCreated();
        $this->post('/api/v2/me/credit-requests', $this->payload(), ['Accept' => 'application/json'])->assertStatus(409);
    }

    public function test_pending_request_can_be_edited_without_reuploading(): void
    {
        Sanctum::actingAs($this->makeBuyer());

        $id = $this->post('/api/v2/me/credit-requests', $this->payload(), ['Accept' => 'application/json'])->json('data.id');

        // Multipart edits go as POST + _method=PUT; title-case "Pending" must not block them.
        $this->post("/api/v2/me/credit-requests/{$id}", [
            '_method'     => 'PUT',
            'amount'      => 3000000,
            'tenure_days' => 90,
            'fields'      => [1 => '09AAACH7409R1ZZ'],
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.amount', 3000000)
            ->assertJsonPath('data.tenure_days', 90);

        $this->assertNotEmpty(CreditWalletRequest::findOrFail($id)->form_data[0]['value'], 'the earlier upload is kept');
    }
}
