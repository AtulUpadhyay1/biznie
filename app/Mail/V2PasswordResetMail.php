<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class V2PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl,
        public string $recipientName,
        public int $expiresInMinutes,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Reset your Biznie password')
            ->view('emails.v2.password-reset', [
                'resetUrl'         => $this->resetUrl,
                'recipientName'    => $this->recipientName,
                'expiresInMinutes' => $this->expiresInMinutes,
            ]);
    }
}
