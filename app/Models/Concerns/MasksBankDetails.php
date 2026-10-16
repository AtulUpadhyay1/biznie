<?php

namespace App\Models\Concerns;

use App\Support\IndianFormat;

/**
 * Masks bank account numbers whenever a wallet or ledger row is serialised.
 *
 * Remarks on these rows are typed by hand in the admin panel ("RTGS from A/c
 * 50100123455300") and the legacy mobile API returns the models as-is, so the
 * customer saw the full number. Masking at serialisation covers every API
 * response — v1 raw models and v2 resources alike — and existing rows, while
 * the admin panel, which reads attributes directly, keeps the full text it
 * needs for reconciliation.
 */
trait MasksBankDetails
{
    /** Free-text columns that may carry an account number. */
    protected function maskedRemarkAttributes(): array
    {
        return ['description', 'notes'];
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($this->maskedRemarkAttributes() as $key) {
            if (isset($attributes[$key]) && is_string($attributes[$key])) {
                $attributes[$key] = IndianFormat::maskAccountNumbersIn($attributes[$key]);
            }
        }

        if (! empty($attributes['transaction_account_number'])) {
            $attributes['transaction_account_number'] = IndianFormat::maskAccountNumber($attributes['transaction_account_number']);
        }

        return $attributes;
    }
}
