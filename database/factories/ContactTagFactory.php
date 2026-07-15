<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use App\Models\Tag;

/**
 * @extends Factory<ContactTag>
 */
class ContactTagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_id' => fn () => Contact::inRandomOrder()->first()->id,
            'tag_id' => fn () => Tag::inRandomOrder()->first()->id,
        ];
    }
}
