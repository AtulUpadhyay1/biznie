<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $formData = is_array($this->form_data) ? $this->form_data : [];

        return [
            'id'                              => $this->id,
            'reference_number'                => $this->reference_number,
            'status'                          => $this->status,
            // pending / approved / rejected — admin writes Title case (B30).
            'status_code'                     => strtolower((string) ($this->status ?: 'pending')),
            'notes'                           => $this->notes,
            'description'                     => $this->description,
            'amount'                          => $this->amount !== null ? (float) $this->amount : null,
            'tenure_days'                     => $this->tenure_days !== null ? (int) $this->tenure_days : null,
            'credit_wallet_document_type_id'  => $this->credit_wallet_document_type_id,
            'document_type_title'             => optional($this->getDocumentType)->name ?: optional($this->getDocumentType)->title,
            // What was submitted, ready to show: files as links, text as values.
            'submitted'                       => array_values(array_map(fn ($f) => [
                'label' => $f['label'] ?? 'Document',
                'type'  => $f['type'] ?? 'text',
                'value' => ($f['type'] ?? 'text') === 'file' ? null : ($f['value'] ?? null),
                'url'   => ($f['type'] ?? 'text') === 'file' && ! empty($f['value']) ? (imageUrl($f['value']) ?: null) : null,
            ], $formData)),
            'document_type'                   => $this->document_type ?? [],
            'form_data'                       => $formData,
            'document'                        => $this->document ?? [],
            'type'                            => $this->type ?? null,
            'created_at'                      => optional($this->created_at)->toIso8601String(),
            'updated_at'                      => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
