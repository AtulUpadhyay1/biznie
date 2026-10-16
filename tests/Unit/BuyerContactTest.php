<?php

namespace Tests\Unit;

use App\Models\CommodityProductOrder;
use App\Models\User;
use App\Support\BuyerContact;
use PHPUnit\Framework\TestCase;

/** Buyer phone/email reach the seller only once the seller confirms (4.11). */
class BuyerContactTest extends TestCase
{
    private function order(string $status): CommodityProductOrder
    {
        $buyer = new User();
        $buyer->id = 7;
        $buyer->name = 'Asha Trading';
        $buyer->phone = '9876543210';
        $buyer->email = 'buyer@example.test';

        $order = new CommodityProductOrder();
        $order->status = $status;
        $order->setRelation('getCustomer', $buyer);

        return $order;
    }

    public function test_hidden_while_booking_and_after_cancelling(): void
    {
        foreach (['pending', 'booked', 'cancelled'] as $status) {
            $c = BuyerContact::customer($this->order($status));
            $this->assertSame('Asha Trading', $c['name'], $status);
            $this->assertNull($c['phone'], $status);
            $this->assertNull($c['email'], $status);
            $this->assertFalse($c['contact_shared'], $status);
        }
    }

    public function test_shared_once_the_seller_confirms(): void
    {
        foreach (['confirm', 'loading', 'dispatched', 'delivered'] as $status) {
            $c = BuyerContact::customer($this->order($status));
            $this->assertSame('9876543210', $c['phone'], $status);
            $this->assertTrue($c['contact_shared'], $status);
        }
    }

    public function test_address_blobs_lose_contact_keys_until_shared(): void
    {
        $blob = ['name' => 'Site office', 'Phone' => '9876543210', 'consignee' => ['email' => 'x@y.in', 'city' => 'Agra']];

        $this->assertSame(
            ['name' => 'Site office', 'Phone' => null, 'consignee' => ['email' => null, 'city' => 'Agra']],
            BuyerContact::scrub($blob, false)
        );
        $this->assertSame($blob, BuyerContact::scrub($blob, true));
    }
}
