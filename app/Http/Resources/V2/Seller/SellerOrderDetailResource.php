<?php

namespace App\Http\Resources\V2\Seller;

use Carbon\Carbon;
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

        // Build richer seller invoice list (drivers first, then seller_invoices array)
        $sellerInvoiceList = [];
        $totalInvoiceAmount = 0.0;
        $drivers = $this->relationLoaded('getDrivers') ? $this->getDrivers : null;
        if ($drivers && $drivers->sum('amount') > 0) {
            foreach ($drivers as $driver) {
                $si = $driver->seller_invoices;
                if (! $si) {
                    continue;
                }
                if (is_string($si)) {
                    $decoded = json_decode($si, true);
                    $si = is_array($decoded) ? $decoded : [];
                }
                $expiry = isset($si['ebill_expiry_date']) ? Carbon::parse($si['ebill_expiry_date']) : null;
                $sellerInvoiceList[] = [
                    'vehicle_number'     => $driver->vehicle_number,
                    'name'               => null,
                    'invoice'            => isset($si['invoice']) ? imageUrl($si['invoice']) : null,
                    'ebill'              => isset($si['ebill']) ? imageUrl($si['ebill']) : null,
                    'ebill_expiry_date'  => $si['ebill_expiry_date'] ?? null,
                    'days_left'          => $expiry ? ($expiry->isToday() ? 0 : ($expiry->isPast() ? 0 : $expiry->diffInDays(now()) + 1)) : null,
                    'is_expired_today'   => $expiry ? $expiry->isToday() : false,
                    'transport_receipt'  => isset($si['transport_receipt']) ? imageUrl($si['transport_receipt']) : null,
                    'amount'             => (float) ($driver->amount ?? 0),
                    'debit_note'         => isset($si['debit_note']) ? imageUrl($si['debit_note']) : null,
                    'debit_note_amount'  => $si['debit_note_amount'] ?? null,
                    'credit_note'        => isset($si['credit_note']) ? imageUrl($si['credit_note']) : null,
                    'credit_note_amount' => $si['credit_note_amount'] ?? null,
                ];
                $totalInvoiceAmount += (float) ($driver->amount ?? 0);
            }
        } elseif (is_array($this->seller_invoices) && count($this->seller_invoices) > 0) {
            foreach ($this->seller_invoices as $inv) {
                $expiry = isset($inv['ebill_expiry_date']) ? Carbon::parse($inv['ebill_expiry_date']) : null;
                $sellerInvoiceList[] = [
                    'vehicle_number'     => null,
                    'name'               => $inv['name'] ?? null,
                    'invoice'            => ! empty($inv['invoice_file']) ? imageUrl($inv['invoice_file']) : null,
                    'ebill'              => ! empty($inv['ebill']) ? imageUrl($inv['ebill']) : null,
                    'ebill_expiry_date'  => $inv['ebill_expiry_date'] ?? null,
                    'days_left'          => $expiry ? ($expiry->isToday() ? 0 : ($expiry->isPast() ? 0 : $expiry->diffInDays(now()) + 1)) : null,
                    'is_expired_today'   => $expiry ? $expiry->isToday() : false,
                    'transport_receipt'  => ! empty($inv['transport_receipt']) ? imageUrl($inv['transport_receipt']) : null,
                    'amount'             => (float) ($inv['amount'] ?? 0),
                    'debit_note'         => ! empty($inv['debit_note']) ? imageUrl($inv['debit_note']) : null,
                    'debit_note_amount'  => $inv['debit_note_amount'] ?? null,
                    'credit_note'        => ! empty($inv['credit_note']) ? imageUrl($inv['credit_note']) : null,
                    'credit_note_amount' => $inv['credit_note_amount'] ?? null,
                ];
                $totalInvoiceAmount += (float) ($inv['amount'] ?? 0);
            }
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
            'ex_price'           => (float) ($this->ex_price ?? 0),
            'freight_advance_amount' => (float) ($this->freight_advance_amount ?? 0),
            'gst'                => $this->gst,
            'seller_commission'  => $this->seller_commission ?? null,
            'commission_type'    => $this->commission_type ?? null,
            'load_within'        => $this->load_within ?? null,
            'credit_days'        => $this->credit_days ?? null,
            'update_for'         => $this->update_for ?? null,
            'purpose'            => $this->purpose ?? null,
            'description'        => $this->description ?? null,
            'selected_quality'   => $this->selected_quality ?? null,
            'selected_packaging_charge' => $this->selected_packaging_charge ?? null,
            'variation'          => $this->variation ?? null,
            'consignee_detail'   => $this->consignee_detail ?? null,
            'billing_address'    => $this->billing_address ?? null,
            'delivery_address'   => $this->delivery_address ?? null,
            'history'            => $this->history ?? [],
            'invoices'           => $invoiceList,
            'seller_invoice_list'   => $sellerInvoiceList,
            'total_invoice_amount'  => $totalInvoiceAmount,
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
