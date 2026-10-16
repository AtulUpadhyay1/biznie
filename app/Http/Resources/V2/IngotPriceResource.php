<?php

namespace App\Http\Resources\V2;

use App\Models\IngotPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngotPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = $this->price !== null ? (float) $this->price : null;
        $previous = $this->previous_price !== null ? (float) $this->previous_price : null;
        $change = ($price !== null && $previous !== null) ? round($price - $previous, 2) : null;

        return [
            'id'         => $this->id,
            'product'    => $this->product ?: IngotPrice::DEFAULT_PRODUCT,
            'location'   => $this->location,
            // Display name: "Mandi Gobindgarh", not "GOBINDGARH (MANDI)".
            'market'     => IngotPrice::marketLabel($this->location),
            'price'      => $price,
            'unit'       => 'MT',
            'previous_price' => $previous,
            'change'     => $change,
            'change_pct' => ($change !== null && $previous) ? round($change / $previous * 100, 2) : null,
            'source'     => $this->source,
            // ISO with offset, so every client renders the same IST time and
            // the year is never lost (B17/B36).
            'date_time'  => $this->resource->observedAt(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
