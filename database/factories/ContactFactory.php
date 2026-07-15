<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'salutation' => fake()->randomElement(['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.']),
            'firstname'=>fake()->firstName(),
            'lastname'=>fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'company' => fake()->company(),
            'mobile' => fake()->phoneNumber(),
        ];
    }
}
