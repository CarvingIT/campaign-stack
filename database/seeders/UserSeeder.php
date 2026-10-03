<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Campaign Stack Admin',
                'email' => 'campaign-stack@carvingit.com',
                'password' => Hash::make('CampaignStack!@#'),
                'email_verified_at' => now(),
                'role_id' => 1, // Admin
            ],
            [
                'name' => 'Arjun Sharma',
                'email' => 'arjun.sharma@campaignstack.in',
                'password' => Hash::make('CampaignStack!@#'),
                'email_verified_at' => now(),
                'role_id' => 1, // Admin
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@campaign-stack.com',
                'password' => Hash::make('CampaignStack!@#'),
                'email_verified_at' => now(),
                'role_id' => 2, // Staff
            ],
            [
                'name' => 'Priya Nair',
                'email' => 'priya.nair@campaignstack.in',
                'password' => Hash::make('CampaignStack!@#'),
                'email_verified_at' => now(),
                'role_id' => 2, // Staff
            ],
        ];

        foreach ($users as $userData) {
            $roleId = $userData['role_id'];
            unset($userData['role_id']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            UserRole::firstOrCreate([
                'user_id' => $user->id,
                'role_id' => $roleId,
            ]);
        }
    }
}

