<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                              => $this->id,
            'reference_number'                => $this->reference_number,
            'status'                          => $this->status,
            'notes'                           => $this->notes,
            'description'                     => $this->description,
            'credit_wallet_document_type_id'  => $this->credit_wallet_document_type_id,
            'document_type_title'             => optional($this->getDocumentType)->title,
            'document_type'                   => $this->document_type ?? [],
            'form_data'                       => $this->form_data ?? [],
            'document'                        => $this->document ?? [],
            'type'                            => $this->type ?? null,
            'created_at'                      => optional($this->created_at)->toIso8601String(),
            'updated_at'                      => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
