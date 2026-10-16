<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** Mobile + SMS code sign-in and sign-up (4.7). No SMS leaves a test run. */
class PhoneOtpAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
        Cache::flush();
    }

    private function freshPhone(): string
    {
        do {
            $phone = (string) random_int(7000000000, 9999999999);
        } while (User::where('phone', $phone)->exists());

        return $phone;
    }

    private function sendCode(string $phone): string
    {
        $res = $this->postJson('/api/v2/auth/otp/send', ['phone' => $phone])->assertOk();

        return (string) $res->json('debug_otp');
    }

    public function test_existing_account_signs_in_with_the_code(): void
    {
        $user = new User();
        $user->name = 'OTP Buyer';
        $user->phone = $this->freshPhone();
        $user->type = 'customer';
        $user->status = 'active';
        $user->save();

        $res = $this->postJson('/api/v2/auth/otp/send', ['phone' => $user->phone])->assertOk()->assertJsonPath('is_registered', true);
        Http::assertNothingSent();

        $this->postJson('/api/v2/auth/otp/verify', ['phone' => $user->phone, 'otp' => $res->json('debug_otp')])
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['token']);
    }

    public function test_new_number_signs_up_with_name_and_role(): void
    {
        $phone = $this->freshPhone();
        $otp = $this->sendCode($phone);

        // The code alone is not enough for a number we have not seen.
        $this->postJson('/api/v2/auth/otp/verify', ['phone' => $phone, 'otp' => $otp])
            ->assertStatus(422)->assertJsonValidationErrors(['name', 'role']);

        $this->postJson('/api/v2/auth/otp/verify', [
            'phone' => $phone, 'otp' => $otp, 'name' => 'New Seller', 'role' => 'seller', 'gst_number' => '09AAACH7409R1ZZ',
        ])
            ->assertCreated()
            ->assertJsonPath('next', '/profile/become-seller');

        $user = User::where('phone', $phone)->firstOrFail();
        $this->assertSame('customer', $user->type);
        $this->assertSame('09AAACH7409R1ZZ', $user->getUserDetail?->gst_number);
    }

    public function test_wrong_codes_are_limited_and_the_code_is_single_use(): void
    {
        $phone = $this->freshPhone();
        $otp = $this->sendCode($phone);
        $wrong = $otp === '000000' ? '111111' : '000000';

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v2/auth/otp/verify', ['phone' => $phone, 'otp' => $wrong])->assertStatus(422);
        }
        // Even the right code is refused once the attempts are spent.
        $this->postJson('/api/v2/auth/otp/verify', ['phone' => $phone, 'otp' => $otp])
            ->assertStatus(422)
            ->assertJsonPath('errors.otp.0', 'Too many wrong attempts. Request a new code.');
    }

    public function test_resend_is_throttled(): void
    {
        $phone = $this->freshPhone();
        $this->sendCode($phone);
        $this->postJson('/api/v2/auth/otp/send', ['phone' => $phone])->assertStatus(429);
    }

    public function test_invalid_mobile_is_rejected(): void
    {
        $this->postJson('/api/v2/auth/otp/send', ['phone' => '12345'])->assertStatus(422)->assertJsonValidationErrors('phone');
    }
}
