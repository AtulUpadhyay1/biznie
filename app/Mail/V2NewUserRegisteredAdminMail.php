<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class V2NewUserRegisteredAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('New user registration on Biznie')
            ->view('emails.v2.admin-new-user-registered', [
                'user' => $this->user,
            ]);
    }
}
