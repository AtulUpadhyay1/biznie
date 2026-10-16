<?php

namespace App\Rules;

use App\Support\IndianFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** 10-digit Indian mobile starting 6–9. See App\Support\IndianFormat. */
class IndianMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! IndianFormat::isMobile(is_scalar($value) ? (string) $value : null)) {
            $fail('Enter a valid 10-digit mobile number');
        }
    }
}
