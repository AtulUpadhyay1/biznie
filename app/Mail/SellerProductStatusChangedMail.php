<?php

namespace App\Mail;

use App\Models\SellerCommodityProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerProductStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SellerCommodityProduct $requestRecord) {}

    public function build(): self
    {
        return $this->subject('Your Biznie product status has been updated')->view('emails.v2.seller-product.status-changed', [
            'requestRecord' => $this->requestRecord,
        ]);
    }
}
