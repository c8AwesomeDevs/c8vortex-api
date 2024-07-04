<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAccountCreation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $maildata)
    {
        $this->maildata = $maildata;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            // gmail needs this 'from' attribute; if unset the email sent will be from 'Laravel'
            from: new Address('c8vortexsupport@calibr8systems.com', 'C8 Vortex Support'),
            subject: 'Welcome to C8-Vortex!',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.newUserMail',
            with: [
                "name" => $this->maildata['name'],
                "email" => $this->maildata['email'],
                "phone_number" => $this->maildata['phone_number'],
                "company_id" => $this->maildata['company_id'],
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
