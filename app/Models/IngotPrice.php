<?php

namespace App\Models;

use App\Support\AddressFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * One market rate: a product, at a market (`location`), at a moment.
 */
class IngotPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['product', 'location', 'source', 'price', 'date_time'];

    /** Products the admin can publish a market rate for. */
    public const PRODUCTS = [
        'MS Ingot',
        'MS Billet',
        'Sponge Iron',
        'TMT Bar (Secondary)',
        'HR Coil',
        'Melting Scrap (HMS)',
    ];

    public const DEFAULT_PRODUCT = 'MS Ingot';

    /** `GOBINDGARH (MANDI)` → `Mandi Gobindgarh`; anything else in proper case. */
    public static function marketLabel(?string $location): string
    {
        $location = trim((string) $location);
        if ($location === '') {
            return '';
        }

        if (preg_match('/^(.+?)\s*\((mandi)\)$/i', $location, $m)) {
            $location = $m[2] . ' ' . $m[1];
        }

        return AddressFormatter::titleCase($location);
    }

    /** `date_time` (IST wall clock) as an ISO-8601 instant with its offset. */
    public function observedAt(): ?string
    {
        $raw = $this->date_time ?: $this->updated_at;
        if (! $raw) {
            return null;
        }

        return Carbon::parse($raw, config('app.timezone'))->toIso8601String();
    }
}
