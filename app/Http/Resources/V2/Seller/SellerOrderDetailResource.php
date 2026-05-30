<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerOrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $invoices = is_array($this->all_invoices ?? null) ? $this->all_invoices : [];
        $invoiceList = [];
        foreach ($invoices as $inv) {
            if (is_array($inv) && ! empty($inv['image'])) {
                $invoiceList[] = [
                    'image'      => imageUrl($inv['image']),
                    'created_at' => $inv['created_at'] ?? null,
                    'type'       => $inv['type'] ?? null,
                ];
            }
        }

        $qcImages = is_array($this->quality_check_image ?? null) ? $this->quality_check_image : [];
        $qcUrls = [];
        foreach ($qcImages as $imgId) {
            $qcUrls[] = imageUrl($imgId);
        }

        return [
            'id'                 => $this->id,
            'unique_id'          => $this->unique_id,
            'order_id'           => $this->order_id,
            'enquiry_id'         => optional($this->getProductEnquiry)->unique_id,
            'status'             => $this->status,
            'origin_city'        => $this->origin_city,
            'base_price'         => (float) ($this->base_price ?? 0),
            'transport_price'    => (float) ($this->transport_price ?? 0),
            'total_quantity'     => $this->total_quantity,
            'final_quantity'     => $this->final_quantity,
            'total_amount'       => (float) ($this->total_amount ?? 0),
            'paid_amount'        => (float) ($this->paid_amount ?? 0),
            'due_amount'         => (float) ($this->due_amount ?? 0),
            'final_amount'       => (float) ($this->final_amount ?? 0),
            'gst'                => $this->gst,
            'seller_commission'  => $this->seller_commission ?? null,
            'commission_type'    => $this->commission_type ?? null,
            'load_within'        => $this->load_within ?? null,
            'selected_quality'   => $this->selected_quality ?? null,
            'selected_packaging_charge' => $this->selected_packaging_charge ?? null,
            'variation'          => $this->variation ?? null,
            'consignee_detail'   => $this->consignee_detail ?? null,
            'billing_address'    => $this->billing_address ?? null,
            'delivery_address'   => $this->delivery_address ?? null,
            'history'            => $this->history ?? [],
            'invoices'           => $invoiceList,
            'quality_check'      => [
                'images'      => $qcUrls,
                'certificate' => $this->quality_check_certificate
                    ? imageUrl($this->quality_check_certificate)
                    : null,
            ],
            'customer' => $this->getCustomer ? [
                'id'    => $this->getCustomer->id,
                'name'  => $this->getCustomer->name,
                'phone' => $this->getCustomer->phone,
                'email' => $this->getCustomer->email,
            ] : null,
            'brand' => $this->getBrand ? [
                'id'   => $this->getBrand->id,
                'name' => $this->getBrand->name,
            ] : null,
            'product' => $this->getCommodityProduct ? [
                'id'        => $this->getCommodityProduct->id,
                'name'      => $this->getCommodityProduct->name,
                'slug'      => $this->getCommodityProduct->slug ?? null,
                'thumbnail' => $this->getCommodityProduct->thumbnail
                    ? imageUrl($this->getCommodityProduct->thumbnail)
                    : null,
                'category' => $this->getCommodityProduct->getCategory ? [
                    'id'   => $this->getCommodityProduct->getCategory->id,
                    'name' => $this->getCommodityProduct->getCategory->name,
                ] : null,
                'unit' => $this->getCommodityProduct->getUnit ? [
                    'id'   => $this->getCommodityProduct->getUnit->id,
                    'name' => $this->getCommodityProduct->getUnit->name,
                ] : null,
            ] : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
