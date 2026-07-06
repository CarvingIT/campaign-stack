<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Campaign Stack Admin',
            'email' => 'campaign-stack@carvingit.com',
            'password'=>bcrypt('CampaignStack!@#'),
            'remember_token'=>0,
            'email_verified_at'=>NOW(),
            'created_at'=>NOW(),
            'updated_at'=>NOW()
        ]);
    }
}
