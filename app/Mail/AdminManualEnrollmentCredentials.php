<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminManualEnrollmentCredentials extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $mailData
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Creative Quad course access is ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-manual-enrollment-credentials',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
