<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class forgetpassword extends Mailable
{
    use Queueable, SerializesModels;
    protected $token;
    /**
     * Create a new message instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $address = env('MAIL_FROM_ADDRESS', 'no-reply@example.com');
        $name = env('MAIL_FROM_NAME', 'starscode');
        $envelop = new Envelope(
            subject: 'forget password',
        );
        $envelop->from(new Address($address, $name));
        return $envelop;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails',
            with: [
                'token' => $this->token
            ]
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
