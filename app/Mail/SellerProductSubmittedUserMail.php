<?php

namespace App\Mail;

use App\Models\SellerCommodityProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerProductSubmittedUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SellerCommodityProduct $requestRecord) {}

    public function build(): self
    {
        return $this->subject('Your Biznie product has been submitted')->view('emails.v2.seller-product.submitted-user', [
            'requestRecord' => $this->requestRecord,
        ]);
    }
}
