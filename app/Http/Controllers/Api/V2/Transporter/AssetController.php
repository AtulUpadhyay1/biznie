<?php

namespace App\Http\Controllers\Api\V2\Transporter;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CommodityProduct;
use App\Models\TransporterAddressPrice;
use App\Models\TransporterDetail;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    private function ownerId(Request $request): int
    {
        $user = $request->user();
        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }

    private function ownerDetail(Request $request): TransporterDetail
    {
        $detail = TransporterDetail::where('user_id', $this->ownerId($request))->first();
        if (! $detail) {
            $detail = new TransporterDetail();
            $detail->user_id = $this->ownerId($request);
            $detail->save();
        }
        return $detail;
    }

    // ---------- Vehicles ----------
    public function vehicles(Request $request): JsonResponse
    {
        $detail = $this->ownerDetail($request);
        $assigned = is_array($detail->vehicle) ? $detail->vehicle : [];

        $list = Vehicle::active()->latest()->get(['id', 'name', 'type', 'capacity', 'photo'])
            ->map(function ($v) use ($assigned) {
                return [
                    'id'          => $v->id,
                    'name'        => $v->name,
                    'type'        => $v->type,
                    'capacity'    => $v->capacity,
                    'photo'       => imageUrl($v->photo),
                    'is_selected' => in_array($v->id, $assigned),
                ];
            });

        return response()->json(['success' => true, 'data' => $list]);
    }

    public function assignVehicles(Request $request): JsonResponse
    {
        $data = $request->validate([
            'vehicle'   => ['nullable', 'array'],
            'vehicle.*' => ['integer', 'min:1'],
        ]);

        $detail = $this->ownerDetail($request);
        $detail->vehicle = $data['vehicle'] ?? [];
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Vehicles assigned successfully.',
        ]);
    }

    // ---------- Commodity Products ----------
    public function products(Request $request): JsonResponse
    {
        $detail = $this->ownerDetail($request);
        $assigned = is_array($detail->commodity_product) ? $detail->commodity_product : [];

        $list = CommodityProduct::active()->latest()->with('getCategory:id,name')
            ->get(['id', 'name', 'thumbnail', 'category_id'])
            ->map(function ($p) use ($assigned) {
                return [
                    'id'            => $p->id,
                    'name'          => $p->name,
                    'thumbnail'     => imageUrl($p->thumbnail),
                    'category_id'   => $p->category_id,
                    'category_name' => optional($p->getCategory)->name,
                    'is_selected'   => in_array($p->id, $assigned),
                ];
            });

        return response()->json(['success' => true, 'data' => $list]);
    }

    public function assignProducts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'commodity_product'   => ['nullable', 'array'],
            'commodity_product.*' => ['integer', 'min:1'],
        ]);

        $detail = $this->ownerDetail($request);
        $detail->commodity_product = $data['commodity_product'] ?? [];
        $detail->save();

        return response()->json([
            'success' => true,
            'message' => 'Products assigned successfully.',
        ]);
    }

    // ---------- Belts (loading/unloading address pricing) ----------
    public function belts(Request $request): JsonResponse
    {
        $list = TransporterAddressPrice::where('user_id', $this->ownerId($request))
            ->get()
            ->groupBy('loading_address')
            ->map(function ($group) {
                return [
                    'loading_address'   => $group->first()->loading_address,
                    'unloading_address' => $group->map(fn ($item) => [
                        'id'        => $item->id,
                        'state'     => $item->state,
                        'city'      => $item->city,
                        'min_price' => $item->min_price,
                        'max_price' => $item->max_price,
                    ])->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return response()->json(['success' => true, 'data' => $list]);
    }

    public function storeBelt(Request $request): JsonResponse
    {
        $data = $request->validate([
            'loading_address'    => ['required', 'string', 'max:255'],
            'entries'            => ['required', 'array', 'min:1'],
            'entries.*.city'     => ['required', 'string', 'max:120'],
            'entries.*.min_price' => ['required', 'numeric', 'min:0'],
            'entries.*.max_price' => ['required', 'numeric', 'gte:entries.*.min_price'],
        ]);

        $userId = $this->ownerId($request);

        foreach ($data['entries'] as $entry) {
            $cityRow = Address::where('city', $entry['city'])->first();

            $row = new TransporterAddressPrice();
            $row->user_id         = $userId;
            $row->loading_address = $data['loading_address'];
            $row->state           = $cityRow ? $cityRow->state : '';
            $row->city            = $entry['city'];
            $row->min_price       = $entry['min_price'];
            $row->max_price       = $entry['max_price'];
            $row->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Belt saved successfully.',
        ]);
    }

    public function destroyBelt(Request $request, int $id): JsonResponse
    {
        $row = TransporterAddressPrice::where('user_id', $this->ownerId($request))->find($id);
        if (! $row) {
            return response()->json(['success' => false, 'message' => 'Entry not found.'], 404);
        }
        $row->delete();
        return response()->json(['success' => true, 'message' => 'Entry removed.']);
    }
}
