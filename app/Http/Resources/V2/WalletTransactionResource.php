<?php

namespace App\Http\Resources\V2;

use App\Support\IndianFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'transaction_id'     => $this->transaction_id,
            'amount'             => (float) ($this->amount ?? 0),
            'status'             => $this->status,
            'mode'               => $this->mode,
            // Admin-typed remarks: mask any bank account number before it reaches
            // the customer (B31).
            'description'        => IndianFormat::maskAccountNumbersIn($this->description),
            'notes'              => IndianFormat::maskAccountNumbersIn($this->notes),
            'transaction_status' => $this->transaction_status,
            'created_at'         => optional($this->created_at)->toIso8601String(),
        ];
    }
}
