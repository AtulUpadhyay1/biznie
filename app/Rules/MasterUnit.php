<?php

namespace App\Rules;

use App\Models\ProductUnit;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** A unit from the product-unit master (name or short name), never free text (B12). */
class MasterUnit implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $unit = mb_strtolower(trim((string) $value));
        $exists = $unit !== '' && ProductUnit::query()
            ->where(fn ($q) => $q->whereRaw('LOWER(name) = ?', [$unit])->orWhereRaw('LOWER(short_name) = ?', [$unit]))
            ->exists();

        if (! $exists) {
            $fail('Select a unit from the list');
        }
    }
}
