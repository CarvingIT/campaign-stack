<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicDbMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectString;
    public $htmlContent;

    public function __construct($subjectString, $htmlContent)
    {
        $this->subjectString = $subjectString;
        $this->htmlContent = $htmlContent;
    }

    public function envelope(): Envelope
    {
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

