<?php

namespace App\Rules;

use App\Support\IndianFormat;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * GSTIN format + state code + check digit, and — when the same request carries
 * a PAN — that the PAN embedded in the GSTIN is that PAN.
 *
 *     'gst' => ['nullable', new Gstin],
 *     'gst_number' => ['required', new Gstin(panField: 'pan_number')],
 */
class Gstin implements ValidationRule, DataAwareRule
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(private ?string $panField = null)
    {
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $gstin = is_scalar($value) ? (string) $value : null;

        if (! IndianFormat::isGstin($gstin)) {
            $fail('GSTIN is invalid');

            return;
        }

        if ($this->panField) {
            $pan = data_get($this->data, $this->panField);
            if (is_string($pan) && $pan !== '' && ! IndianFormat::gstinMatchesPan($gstin, $pan)) {
                $fail('GSTIN does not match PAN');
            }
        }
    }
}
