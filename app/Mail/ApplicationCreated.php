<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SebastianBergmann\Type\VoidType;

class ApplicationCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // return new Envelope(
        //     subject: 'Application Created',
        // );
        return new Envelope(
            from: new Address('jeffrey@example.com', 'Jeffrey Way'),
            subject: 'Application Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!is_null($this->application->file_url))
            return [
                Attachment::fromStorageDisk('public', $this->application->file_url)
            ];
        else return [];
    }
}
