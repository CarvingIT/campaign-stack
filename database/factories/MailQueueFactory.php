<?php

namespace Database\Factories;

use App\Models\MailQueue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MailQueue>
 */
class MailQueueFactory extends Factory
{
    protected $model = MailQueue::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'newsletter_id' => null,
            'contact_id' => null,
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraph(),
            'status' => 'Q',
            'attempt' => 0,
            'sending_attempted_at' => null,
            'response_code' => null,
            'error' => null,
            'outbound_mail_account_id' => null,
        ];
    }
}