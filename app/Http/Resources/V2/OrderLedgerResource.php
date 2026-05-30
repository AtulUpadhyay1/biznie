<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderLedgerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'order_id'           => $this->order_id,
            'transaction_id'     => $this->transaction_id,
            'type'               => $this->type,
            'amount'             => (float) ($this->amount ?? 0),
            'remaining_balance'  => $this->remaining_balance !== null ? (float) $this->remaining_balance : null,
            'description'        => $this->description,
            'payment_mode'       => $this->payment_mode ?? null,
            'payment_method'     => $this->payment_method ?? null,
            'transaction_number' => $this->transaction_number ?? null,
            'transaction_bank_name' => $this->transaction_bank_name ?? null,
            'date_time'          => $this->date_time ?? null,
            'file'               => $this->file ? imageUrl($this->file) : null,
            'created_at'         => optional($this->created_at)->toIso8601String(),
        ];
    }
}
