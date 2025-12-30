<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
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
            'wallet_document_type_name' => $this->getDocumentType ? $this->getDocumentType->name : null,
            'document'                  => [],
            'document_type'             => $this->document_type,
            'form_data'                 => $this->form_data,
            'created_at'                => dateTimeFormat($this->created_at),
            'updated_at'                => dateTimeFormat($this->updated_at)
        ];

        if($this->document){
            foreach ($this->document as $document) {
                $data['document'][]     = imageUrl($document);
            }
        }


        if($this->form_data){
            $form_data = [];
            foreach ($this->form_data as $key => $form_value) {
                if(is_array($form_value) && isset($form_value['type']) && $form_value['type'] == 'file' && isset($form_value['value'])){
                    $form_value['value'] = imageUrl($form_value['value']);
                }
                $form_data[$key] = $form_value;
            }
            $data['form_data'] = $form_data;
        }

        return $data;
    }
}
