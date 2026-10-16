<?php

namespace App\Rules;

use App\Support\IndianFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** 6-digit Indian pincode, no leading zero. See App\Support\IndianFormat. */
class Pincode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! IndianFormat::isPincode(is_scalar($value) ? (string) $value : null)) {
            $fail('Enter a valid 6-digit pincode');
        }
    }
}
