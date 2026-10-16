<?php

namespace App\Rules;

use App\Support\IndianFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HsnCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! IndianFormat::isHsn(is_scalar($value) ? (string) $value : null)) {
            $fail('HSN must be 4, 6 or 8 digits');
        }
    }
}
