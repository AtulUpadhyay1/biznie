<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class V2RegisterOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public int $expiresInMinutes,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Your Biznie verification code: '.$this->otp)
            ->view('emails.v2.register-otp', [
                'otp'              => $this->otp,
                'expiresInMinutes' => $this->expiresInMinutes,
            ]);
    }
}
