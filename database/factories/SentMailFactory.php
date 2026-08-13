<?php

namespace Database\Factories;

use App\Models\SentMail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SentMail>
 */
class SentMailFactory extends Factory
{
    protected $model = SentMail::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'newsletter_id' => null,
            'outbound_mail_account_id' => null,
            'contact_id' => null,
            'subject' => fake()->sentence(6),
            'body' => fake()->paragraph(),
            'opened' => fake()->boolean(),
        ];
    }
}