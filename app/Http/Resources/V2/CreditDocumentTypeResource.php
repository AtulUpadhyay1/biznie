<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditDocumentTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $forms = is_array($this->forms) ? $this->forms : (json_decode((string) $this->forms, true) ?: []);

        return [
            'id'          => $this->id,
            // Admin saves `name`; `title` was never filled, so every option
            // read "—" on the storefront (B30).
            'name'        => $this->name ?: $this->title,
            'title'       => $this->title ?: $this->name,
            'description' => $this->description,
            // One shape for the form to render: index = position in `forms`,
            // which is how a submission is matched back to its field.
            'fields'      => array_values(array_map(fn ($f, $i) => [
                'index'       => $i,
                'label'       => trim((string) ($f['label'] ?? 'Document')),
                'type'        => in_array($f['type'] ?? 'text', ['file', 'number', 'text'], true) ? $f['type'] : 'text',
                'required'    => filter_var($f['required'] ?? false, FILTER_VALIDATE_BOOL),
                'description' => $f['description'] ?? null,
            ], $forms, array_keys($forms))),
            'forms'       => $forms,
            'status'      => $this->status,
        ];
    }
}
