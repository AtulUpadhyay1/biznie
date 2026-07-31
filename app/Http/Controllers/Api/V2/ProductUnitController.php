<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\ProductUnit;
use Illuminate\Http\JsonResponse;

class ProductUnitController extends Controller
{
    /**
     * Units a size variant can be measured in.
     *
     * Admin-managed master data, and the same list the catalog labels its
     * variations with ("8 MM", "50 Mt") — so a seller's own variants read the
     * same way as a catalog product's.
     */
    public function index(): JsonResponse
    {
        $units = ProductUnit::active()
            ->orderBy('name')
            ->get(['id', 'name', 'short_name'])
            ->map(fn ($unit) => [
                'id'         => $unit->id,
                'name'       => $unit->name,
                'short_name' => $unit->short_name,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $units,
        ]);
    }
}
