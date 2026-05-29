<?php

namespace App\Http\Controllers\Api\V2\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\AddressResource;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $list = UserAddress::where('user_id', $request->user()->id)->latest()->get();

        return response()->json([
            'success' => true,
            'data'    => AddressResource::collection($list),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->rules($request);

        $address = new UserAddress();
        $address->user_id = $request->user()->id;
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
        $address = UserAddress::where('user_id', $request->user()->id)->find($id);
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
        $address = UserAddress::where('user_id', $request->user()->id)->find($id);
        if (! $address) {
            return response()->json(['success' => false, 'message' => 'Address not found.'], 404);
        }
        $address->delete();

        return response()->json(['success' => true, 'message' => 'Address removed.']);
    }

    private function rules(Request $request): array
    {
        return $request->validate([
            'pincode'          => ['required', 'string', 'max:10'],
            'address_line_one' => ['required', 'string', 'max:255'],
            'address_line_two' => ['nullable', 'string', 'max:255'],
            'city'             => ['required', 'string', 'max:80'],
            'state'            => ['required', 'string', 'max:80'],
            'country'          => ['nullable', 'string', 'max:80'],
            'company_name'     => ['nullable', 'string', 'max:160'],
            'phone'            => ['required', 'string', 'max:20'],
            'gst'              => ['nullable', 'string', 'max:30'],
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
