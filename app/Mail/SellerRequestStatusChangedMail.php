<?php

namespace App\Mail;

use App\Models\SellerOnboardingDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerRequestStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SellerOnboardingDetail $requestRecord) {}

    public function build(): self
    {
        return $this->subject('Your Biznie seller request status has been updated')->view('emails.v2.seller-request.status-changed', [
            'requestRecord' => $this->requestRecord,
        ]);
    }
}
