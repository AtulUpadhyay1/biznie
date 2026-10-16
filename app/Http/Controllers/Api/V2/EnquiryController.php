<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\GeneralEnquiry;
use App\Models\SellerCommodityProduct;
use App\Rules\Gstin;
use App\Rules\IndianMobile;
use App\Rules\OrderQuantity;
use App\Rules\Pincode;
use App\Support\IndianFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // The listing, when the lead came from a product page, carries the MOQ
        // the quantity is checked against.
        $listing = $request->integer('seller_commodity_product_id')
            ? SellerCommodityProduct::with('getCommodityProduct')->find($request->integer('seller_commodity_product_id'))
            : null;

        $data = $request->validate([
            'name'              => ['required', 'string', 'max:120'],
            'company_name'      => ['nullable', 'string', 'max:160'],
            'contact_number'    => ['required', 'string', new IndianMobile],
            'email'             => ['nullable', 'email', 'max:160'],
            'gst_number'        => ['nullable', 'string', new Gstin],
            // Structured fields from the home quote forms. They used to be
            // folded into `requirement` as free text, which is how "-5" and
            // a 2-digit pincode got through (B01/B02).
            'product'           => ['nullable', 'string', 'max:160'],
            'quantity'          => ['nullable', new OrderQuantity(
                $listing?->getCommodityProduct?->min_order_qty,
                $request->string('quantity_unit')->toString() ?: 'MT'
            )],
            'quantity_unit'     => ['nullable', 'string', 'max:20'],
            'pincode'           => ['nullable', 'string', new Pincode],
            'delivery_location' => ['nullable', 'string', 'max:255', function (string $attribute, mixed $value, \Closure $fail) {
                // Older clients send the pincode in this field.
                if (is_string($value) && ctype_digit($value) && ! IndianFormat::isPincode($value)) {
                    $fail('Enter a valid 6-digit pincode');
                }
            }],
            'brand_id'          => ['nullable', 'integer', 'exists:brands,id'],
            'seller_commodity_product_id' => ['nullable', 'integer'],
            'requirement'       => ['nullable', 'string', 'max:2000'],
            'message'           => ['nullable', 'string', 'max:2000'],
        ]);

        $requirement = $data['requirement'] ?? null;
        if (! $requirement && (! empty($data['product']) || ! empty($data['quantity']))) {
            $quantity = ! empty($data['quantity'])
                ? rtrim(rtrim((string) $data['quantity'], '0'), '.') . ' ' . ($data['quantity_unit'] ?? 'MT')
                : null;
            $requirement = implode(' — ', array_filter([$data['product'] ?? null, $quantity]));
        }

        $enquiry = new GeneralEnquiry();
        $enquiry->unique_id                   = 'ENQ-'.strtoupper(Str::random(8));
        $enquiry->user_id                     = $request->user('sanctum')?->id; // optional bearer on a public route
        $enquiry->name                        = $data['name'];
        $enquiry->company_name                = $data['company_name'] ?? null;
        $enquiry->contact_number              = $data['contact_number'];
        $enquiry->email                       = $data['email'] ?? null;
        $enquiry->gst_number                  = isset($data['gst_number']) ? strtoupper($data['gst_number']) : null;
        $enquiry->delivery_location           = $data['pincode'] ?? $data['delivery_location'] ?? null;
        $enquiry->brand_id                    = $data['brand_id'] ?? null;
        $enquiry->seller_commodity_product_id = $data['seller_commodity_product_id'] ?? null;
        $enquiry->requirement                 = $requirement;
        $enquiry->message                     = $data['message'] ?? null;
        $enquiry->status                      = 'pending';
        $enquiry->save();

        // Puts it in the admin bell and on the dashboard, which is what makes a
        // walk-in enquiry visible to anyone.
        sendAdminNotification(
            'New general enquiry',
            trim(($enquiry->name ?: 'A visitor').' sent a requirement'.($enquiry->requirement ? ': '.$enquiry->requirement : '')),
            'general_enquiry',
            ['id' => $enquiry->id, 'unique_id' => $enquiry->unique_id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Enquiry submitted successfully.',
            'data'    => ['reference' => $enquiry->unique_id],
        ], 201);
    }
}
