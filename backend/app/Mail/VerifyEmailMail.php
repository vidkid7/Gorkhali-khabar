<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $recipientName, public string $verifyUrl, public string $language = 'ne') {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->language === 'en' ? 'Email Verification' : 'इमेल प्रमाणीकरण');
    }

    public function content(): Content
    {
        $name = e($this->recipientName);
        $url = e($this->verifyUrl);
        $body = $this->language === 'en'
            ? "Hello {$name}, click the link below to verify your email. This link expires in 24 hours."
            : "नमस्कार {$name}, आफ्नो इमेल प्रमाणित गर्न तलको लिंकमा क्लिक गर्नुहोस्। यो लिंक २४ घण्टामा समाप्त हुनेछ।";

        return new Content(htmlString: "<p>{$body}</p><p><a href=\"{$url}\">{$url}</a></p>");
    }
}