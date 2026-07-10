<?php
namespace App\Mail\Transports;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class CustomApiTransport extends AbstractTransport
{
    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        parent::__construct();
        $this->apiKey = $apiKey;
    }

    protected function doSend(SentMessage $message): void
    {
        // Convert Symfony message to Email instance
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        // Implement your custom API sending logic here
        // Example: Http::withToken($this->apiKey)->post('...', [...]);
    }

    public function __toString(): string
    {
        return 'custom-api';
    }
}
