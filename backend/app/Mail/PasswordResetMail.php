<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $recipientName, public string $resetUrl, public string $language = 'ne') {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->language === 'en' ? 'Password Reset' : 'पासवर्ड रिसेट');
    }

    public function content(): Content
    {
        $name = e($this->recipientName);
        $url = e($this->resetUrl);
        $body = $this->language === 'en'
            ? "Hello {$name}, click the link below to reset your password. This link expires in one hour."
            : "नमस्कार {$name}, आफ्नो पासवर्ड रिसेट गर्न तलको लिंकमा क्लिक गर्नुहोस्। यो लिंक एक घण्टामा समाप्त हुनेछ।";

        return new Content(htmlString: "<p>{$body}</p><p><a href=\"{$url}\">{$url}</a></p>");
    }
}