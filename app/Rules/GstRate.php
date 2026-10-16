<?php

namespace App\Rules;

use App\Support\IndianFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** One of the GST slabs (0, 5, 12, 18, 28) — never a free-typed percentage. */
class GstRate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! IndianFormat::isGstRate($value)) {
            $fail('GST rate must be one of ' . implode(', ', IndianFormat::GST_RATES) . '%');
        }
    }
}
