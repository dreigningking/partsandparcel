<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $contactData)
    {
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address') ?: 'support@partsandparcel.com';
        $fromName = config('mail.from.name') ?: config('app.name', 'Parts & Parcel');
        $topic = $this->contactData['topic'] ?? 'General Inquiry';
        $subject = $this->contactData['subject'] ?? 'New Contact Inquiry';

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: [
                new Address($this->contactData['email'], $this->contactData['name'])
            ],
            subject: "[{$topic}] {$subject} — Parts & Parcel Support",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-message',
        );
    }
}
