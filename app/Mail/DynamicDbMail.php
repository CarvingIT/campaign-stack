<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicDbMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectString;
    public $htmlContent;
    public $fromAddress;
    public $fromName;

    public function __construct($subjectString, $htmlContent, $fromAddress = null, $fromName = null)
    {
        $this->subjectString = $subjectString;
        $this->htmlContent = $htmlContent;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName ?? 'Campaign Stack';
    }

    public function envelope(): Envelope
    {
        if (!empty($this->fromAddress)) {
            return new Envelope(
                from: new Address($this->fromAddress, $this->fromName),
                subject: $this->subjectString,
            );
        }

        return new Envelope(
            subject: $this->subjectString,
        );
    }

    public function content(): Content
    {
        // Renders raw HTML string directly instead of a Blade file path
        return new Content(
            htmlString: $this->htmlContent,
        );
    }
}
