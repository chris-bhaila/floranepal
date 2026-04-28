<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $url
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email — FloraNepal 🌿',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.verify',
        );
    }
}