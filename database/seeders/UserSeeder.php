<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // add sample users 
        User::create(
            [
            'name' => 'Campaign Stack Admin',
            'email' => 'campaign-stack@carvingit.com',
            'password'=>bcrypt('CampaignStack!@#'),
            'remember_token'=>0,
            'email_verified_at'=>NOW(),
            'created_at'=>NOW(),
            'updated_at'=>NOW()
            ]
        );
        User::create(
            [
            'name' => 'Staff User',
            'email' => 'staff@campaign-stack.com',
            'password'=>bcrypt('CampaignStack!@#'),
            'remember_token'=>0,
            'email_verified_at'=>NOW(),
            'created_at'=>NOW(),
            'updated_at'=>NOW()
            ]
        );

        UserRole::create([
            'user_id'=>1,
            'role_id'=>1
        ]);
        UserRole::create([
            'user_id'=>2,
            'role_id'=>2
        ]);
    }
}

