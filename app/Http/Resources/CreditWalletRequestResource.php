<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\CreditWalletDocumentType;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditWalletRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $data = [
            'id'                        => $this->id,
            'reference_number'          => $this->reference_number,
            'status'                    => $this->status,
            'notes'                     => $this->notes,
            'description'               => $this->description,
            'wallet_document_type_name' => CreditWalletDocumentType::find($this->credit_wallet_document_type_id)->name,
            'document'                  => [],
            'document_type'             => $this->document_type,
            'created_at'                => dateTimeFormat($this->created_at),
            'updated_at'                => dateTimeFormat($this->updated_at)
        ];

        if($this->document){
            foreach ($this->document as $document) {
                $data['document'][]     = imageUrl($document);
            }
        }

        return $data;
    }
}
