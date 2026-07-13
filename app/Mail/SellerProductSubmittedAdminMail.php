<?php

namespace App\Mail;

use App\Models\SellerCommodityProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerProductSubmittedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SellerCommodityProduct $requestRecord) {}

    public function build(): self
    {
        return $this->subject('New product submitted for review')->view('emails.v2.seller-product.submitted-admin', [
            'requestRecord' => $this->requestRecord,
        ]);
    }
}
