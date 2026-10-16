<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A positive quantity with at most two decimals, and at least the product's
 * minimum order when one is set.
 *
 *     'quantity' => ['required', new OrderQuantity(minOrder: $product->min_order_qty, unit: 'MT')],
 */
class OrderQuantity implements ValidationRule
{
    public function __construct(private float|int|string|null $minOrder = null, private string $unit = 'MT')
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value) || (float) $value <= 0) {
            $fail('Quantity must be greater than 0');

            return;
        }

        if (preg_match('/^\d+(\.\d{1,2})?$/', (string) $value) !== 1) {
            $fail('Quantity can have at most 2 decimals');

            return;
        }

        $min = (float) ($this->minOrder ?? 0);
        if ($min > 0 && (float) $value < $min) {
            $fail('Minimum order is ' . rtrim(rtrim(number_format($min, 2, '.', ''), '0'), '.') . ' ' . $this->unit . ' for this product');
        }
    }
}
