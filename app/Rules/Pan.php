<?php

namespace App\Rules;

use App\Support\IndianFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Pan implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! IndianFormat::isPan(is_scalar($value) ? (string) $value : null)) {
            $fail('Enter a valid PAN');
        }
    }
}
