<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Campaign::create(
            [
                'name'=>'Koha'
            ]
        );
        Campaign::create(
            [
                'name'=>'Smart Repository'
            ]
        );
        Campaign::create(
            [
                'name'=>'General'
            ]
        );
        Campaign::create(
            [
                'name'=>'Cyber security'
            ]
        );
    }
}
