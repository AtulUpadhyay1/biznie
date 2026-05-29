<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\GeneralEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:120'],
            'company_name'      => ['nullable', 'string', 'max:160'],
            'contact_number'    => ['required', 'string', 'max:20'],
            'email'             => ['required', 'email', 'max:160'],
            'gst_number'        => ['nullable', 'string', 'max:30'],
            'delivery_location' => ['nullable', 'string', 'max:255'],
            'brand_id'          => ['nullable', 'integer', 'exists:brands,id'],
            'seller_commodity_product_id' => ['nullable', 'integer'],
            'requirement'       => ['nullable', 'string', 'max:2000'],
            'message'           => ['nullable', 'string', 'max:2000'],
        ]);

        $enquiry = new GeneralEnquiry();
        $enquiry->unique_id                   = 'ENQ-'.strtoupper(Str::random(8));
        $enquiry->user_id                     = $request->user()?->id;
        $enquiry->name                        = $data['name'];
        $enquiry->company_name                = $data['company_name'] ?? null;
        $enquiry->contact_number              = $data['contact_number'];
        $enquiry->email                       = $data['email'];
        $enquiry->gst_number                  = $data['gst_number'] ?? null;
        $enquiry->delivery_location           = $data['delivery_location'] ?? null;
        $enquiry->brand_id                    = $data['brand_id'] ?? null;
        $enquiry->seller_commodity_product_id = $data['seller_commodity_product_id'] ?? null;
        $enquiry->requirement                 = $data['requirement'] ?? null;
        $enquiry->message                     = $data['message'] ?? null;
        $enquiry->status                      = 'pending';
        $enquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Enquiry submitted successfully.',
            'data'    => ['reference' => $enquiry->unique_id],
        ], 201);
    }
}
