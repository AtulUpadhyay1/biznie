<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Address;

class AddressLookupController extends Controller
{
    public function pincode(string $pincode)
    {
        $row = Address::where('pincode', $pincode)
            ->first(['pincode', 'city', 'state', 'country']);

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Pincode not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pincode' => (string) $row->pincode,
                'city' => (string) $row->city,
                'state' => (string) $row->state,
                'country' => (string) ($row->country ?? 'India'),
            ],
        ]);
    }

    public function states()
    {
        $states = Address::query()
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->values();

        return response()->json(['success' => true, 'data' => $states]);
    }

    /**
     * Cities in a state, each listed once.
     *
     * Grouped in the database rather than after the fact: `addresses` is a
     * pincode table, so a state can hold thousands of rows for a few hundred
     * cities and de-duplicating in PHP meant loading all of them per request.
     */
    public function cities(string $state)
    {
        $cities = Address::query()
            ->where('state', $state)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        return response()->json(['success' => true, 'data' => $cities]);
    }
}
