<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApproval extends Mailable
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
            subject: 'Pending: C8-Vortex Account Approval',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $approvehash = hash('murmur3c', env('APPROVERS_API_KEY') . "approve" . 'salt and pepper', false);
        $declinehash = hash('murmur3c', env('APPROVERS_API_KEY') . "decline" . 'salt and pepper', false);
        return new Content(
            view: 'emails.requestApprovalMail',
            with: [
                "name" => $this->maildata['name'],
                "email" => $this->maildata['email'],
                "phone_number" => $this->maildata['phone_number'],
                "company_name" => $this->maildata['company_name'],
                "approver" => $this->maildata['approver'],
                "linkApprove" => env('API_HOST') . 'approver/useractivation?user=' . $this->maildata['email'] . '&action=approve' . '&k=' . $approvehash,
                "linkDecline" => env('API_HOST') . 'approver/useractivation?user=' . $this->maildata['email'] . '&action=decline' . '&k=' . $declinehash,
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
