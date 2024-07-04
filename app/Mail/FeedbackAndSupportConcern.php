<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class FeedbackAndSupportConcern extends Mailable
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
            from: new Address('c8vortexsupport@calibr8systems.com', 'C8 Vortex API'),
            subject: 'Feedback And Support Concern',
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
            view: 'emails.newFeedbackAndConcernMail',
            with: [
                "datetime" => $this->maildata['timestamp_sent'],
                "name" => $this->maildata['user_fullname'],
                "email" => $this->maildata['user_email'],
                "phone_number" => $this->maildata['user_phone'],
                "company_name" => $this->maildata['company_name'],
                "company_country" => $this->maildata['company_country'],
                "company_domain" => $this->maildata['company_domain'],
                "user_concern" => $this->maildata['user_concern'],
                "approver" => $this->maildata['approver'],
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
