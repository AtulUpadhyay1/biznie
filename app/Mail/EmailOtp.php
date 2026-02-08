<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;
    public $expiryMinutes;
    public $userEmail;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @param string $otp The OTP code to send
     * @param string $userName The name of the user
     * @param string $userEmail The email address of the user
     * @param int $expiryMinutes Number of minutes until OTP expires
     * @param string $subject The email subject
     */
    public function __construct($otp, $userName = null, $userEmail = null, $expiryMinutes = 10, $subject = null)
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->expiryMinutes = $expiryMinutes;
        $this->subject = $subject ?? 'Email Verification Code - ' . config('app.name', 'Biznie');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->userEmail,
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp' => $this->otp,
                'userName' => $this->userName,
                'expiryMinutes' => $this->expiryMinutes,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
