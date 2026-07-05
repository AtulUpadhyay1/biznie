<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class V2WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Welcome to Biznie')
            ->view('emails.v2.welcome-user', [
                'recipientName' => $this->recipientName,
            ]);
    }
}
