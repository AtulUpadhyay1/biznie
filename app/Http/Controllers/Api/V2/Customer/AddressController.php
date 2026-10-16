<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\AddressResource;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Rules\Gstin;
use App\Rules\IndianMobile;
use App\Rules\Pincode;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $list = UserAddress::where('user_id', $this->ownerId($request))->latest()->get();

        return response()->json([
            'success' => true,
            'data'    => AddressResource::collection($list),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->rules($request);

        $address = new UserAddress();
        $address->user_id = $this->ownerId($request);
        $this->assign($address, $data);
        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'data'    => new AddressResource($address),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', $this->ownerId($request))->find($id);
        if (! $address) {
            return response()->json(['success' => false, 'message' => 'Address not found.'], 404);
        }

        $data = $this->rules($request);
        $this->assign($address, $data);
        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data'    => new AddressResource($address),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', $this->ownerId($request))->find($id);
        if (! $address) {
            return response()->json(['success' => false, 'message' => 'Address not found.'], 404);
        }
        $address->delete();

        return response()->json(['success' => true, 'message' => 'Address removed.']);
    }

    /**
     * Addresses belong to the company account. Staff used to read and write
     * their own (empty) list, so the RFQ form showed "No saved addresses" to
     * every staff login (B26).
     */
    private function ownerId(Request $request): int
    {
        $user = $request->user();

        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }

    private function rules(Request $request): array
    {
        $this->uppercaseInputs($request, 'gst');

        return $request->validate([
            'pincode'          => ['required', 'string', new Pincode],
            'address_line_one' => ['required', 'string', 'max:255'],
            'address_line_two' => ['nullable', 'string', 'max:255'],
            'city'             => ['required', 'string', 'max:80'],
            'state'            => ['required', 'string', 'max:80'],
            'country'          => ['nullable', 'string', 'max:80'],
            'company_name'     => ['nullable', 'string', 'max:160'],
            'phone'            => ['required', 'string', new IndianMobile],
            'gst'              => ['nullable', 'string', new Gstin],
        ]);
    }

    private function assign(UserAddress $address, array $data): void
    {
        $address->pincode          = $data['pincode'];
        $address->address_line_one = $data['address_line_one'];
        $address->address_line_two = $data['address_line_two'] ?? null;
        $address->city             = $data['city'];
        $address->state            = $data['state'];
        $address->country          = $data['country'] ?? $address->country ?? 'India';
        $address->company_name     = $data['company_name'] ?? null;
        $address->phone            = $data['phone'];
        $address->gst              = $data['gst'] ?? null;
    }
}
