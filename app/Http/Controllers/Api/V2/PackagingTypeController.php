<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\PackagingType;
use Illuminate\Http\JsonResponse;

class PackagingTypeController extends Controller
{
    /**
     * The packaging types a seller may offer.
     *
     * Admin-managed master data, so it is a short flat list rather than a
     * paginated one: the product form renders every option as a checkbox and a
     * seller cannot invent a type of their own.
     */
    public function index(): JsonResponse
    {
        $types = PackagingType::active()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($type) => [
                'id'   => $type->id,
                'name' => $type->name,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $types,
        ]);
    }
}
