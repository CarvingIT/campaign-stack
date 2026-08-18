<?php

namespace Database\Factories;

use App\Models\OutboundMailAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutboundMailAccount>
 */
class OutboundMailAccountFactory extends Factory
{
    protected $model = OutboundMailAccount::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company() . ' Mail',
            'type' => 'smtp',
            'active_after' => now(),
            'config' => null,
        ];
    }
}