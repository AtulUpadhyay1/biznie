<?php

namespace Tests\Unit;

use App\Rules\Gstin;
use App\Rules\GstRate;
use App\Rules\HsnCode;
use App\Rules\IndianMobile;
use App\Rules\OrderQuantity;
use App\Rules\Pincode;
use App\Support\IndianFormat;
use PHPUnit\Framework\TestCase;

/**
 * The values here are the ones the QA run (Oct 2026) pushed through every form:
 * 12345, -5, 12, ZYABCDE1234F1ZE, 1820%, test213. Each must be rejected.
 */
class IndianFormatTest extends TestCase
{
    public function test_mobile(): void
    {
        $this->assertTrue(IndianFormat::isMobile('9876543210'));
        $this->assertTrue(IndianFormat::isMobile(' 6000000000 '));
        $this->assertFalse(IndianFormat::isMobile('12345'));
        $this->assertFalse(IndianFormat::isMobile('5544332211'));
        $this->assertFalse(IndianFormat::isMobile('98765432101'));
        $this->assertFalse(IndianFormat::isMobile(null));
    }

    public function test_pincode(): void
    {
        $this->assertTrue(IndianFormat::isPincode('221002'));
        $this->assertFalse(IndianFormat::isPincode('12'));
        $this->assertFalse(IndianFormat::isPincode('021002'));
        $this->assertFalse(IndianFormat::isPincode('2210021'));
    }

    public function test_gstin_checksum_and_state(): void
    {
        // GSTN's published sample.
        $this->assertTrue(IndianFormat::isGstin('27AAPFU0939F1ZV'));
        $this->assertTrue(IndianFormat::isGstin('27aapfu0939f1zv'));
        // The QA value: right shape, wrong state code and wrong check digit.
        $this->assertFalse(IndianFormat::isGstin('ZYABCDE1234F1ZE'));
        // One character off from the valid sample.
        $this->assertFalse(IndianFormat::isGstin('27AAPFU0939F1ZW'));
        // State 45 does not exist.
        $this->assertFalse(IndianFormat::isGstin('45AAPFU0939F1Z' . IndianFormat::gstinCheckDigit('45AAPFU0939F1Z')));
    }

    public function test_gstin_pan_cross_check(): void
    {
        $this->assertSame('AAPFU0939F', IndianFormat::panFromGstin('27AAPFU0939F1ZV'));
        $this->assertTrue(IndianFormat::gstinMatchesPan('27AAPFU0939F1ZV', 'aapfu0939f'));
        $this->assertFalse(IndianFormat::gstinMatchesPan('27AAPFU0939F1ZV', 'ABCDE1234F'));
    }

    public function test_pan_hsn_gst_rate(): void
    {
        $this->assertTrue(IndianFormat::isPan('ABCDE1234F'));
        $this->assertFalse(IndianFormat::isPan('ABCDE1234'));

        $this->assertTrue(IndianFormat::isHsn('7214'));
        $this->assertTrue(IndianFormat::isHsn('721420'));
        $this->assertTrue(IndianFormat::isHsn('72142090'));
        $this->assertFalse(IndianFormat::isHsn('test213'));
        $this->assertFalse(IndianFormat::isHsn('72142'));

        $this->assertTrue(IndianFormat::isGstRate(18));
        $this->assertTrue(IndianFormat::isGstRate('5'));
        $this->assertTrue(IndianFormat::isGstRate('0'));
        $this->assertFalse(IndianFormat::isGstRate('1820'));
        $this->assertFalse(IndianFormat::isGstRate('abc'));
    }

    public function test_account_numbers_are_masked_in_remarks(): void
    {
        $this->assertSame('XXXX5300', IndianFormat::maskAccountNumber('5010 0123 4553 00'));
        $this->assertSame(
            'RTGS from A/cXXXX5300 for OID-20260514-1230 on 01/12/25, amt 4500000',
            IndianFormat::maskAccountNumbersIn('RTGS from A/c50100123455300 for OID-20260514-1230 on 01/12/25, amt 4500000')
        );
        $this->assertNull(IndianFormat::maskAccountNumbersIn(null));
    }

    public function test_rules_fail_with_the_documented_messages(): void
    {
        $this->assertRuleFails(new IndianMobile, '12345', 'Enter a valid 10-digit mobile number');
        $this->assertRuleFails(new Pincode, '12', 'Enter a valid 6-digit pincode');
        $this->assertRuleFails(new HsnCode, 'test213', 'HSN must be 4, 6 or 8 digits');
        $this->assertRuleFails(new GstRate, '1820', 'GST rate must be one of 0, 5, 12, 18, 28%');
        $this->assertRuleFails(new OrderQuantity, '-5', 'Quantity must be greater than 0');
        $this->assertRuleFails(new OrderQuantity(30), '12', 'Minimum order is 30 MT for this product');
        $this->assertRuleFails(new OrderQuantity, '1.234', 'Quantity can have at most 2 decimals');
        $this->assertRuleFails(new Gstin, 'ZYABCDE1234F1ZE', 'GSTIN is invalid');

        $rule = (new Gstin(panField: 'pan'))->setData(['pan' => 'ABCDE1234F']);
        $this->assertRuleFails($rule, '27AAPFU0939F1ZV', 'GSTIN does not match PAN');

        $this->assertRulePasses(new OrderQuantity(30), '30');
        $this->assertRulePasses(new OrderQuantity(30), '30.50');
        $this->assertRulePasses(new Gstin, '27AAPFU0939F1ZV');
    }

    private function assertRuleFails(object $rule, mixed $value, string $message): void
    {
        $messages = [];
        $rule->validate('field', $value, function (string $m) use (&$messages) {
            $messages[] = $m;
        });
        $this->assertSame([$message], $messages);
    }

    private function assertRulePasses(object $rule, mixed $value): void
    {
        $messages = [];
        $rule->validate('field', $value, function (string $m) use (&$messages) {
            $messages[] = $m;
        });
        $this->assertSame([], $messages);
    }
}
