<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = [
            'Q3 SaaS Growth Outreach - India',
            'Koha LMS Enterprise Upgrade 2026',
            'Cloud Security & Compliance Summit - Bangalore',
            'AI Automation & Workflow Pilot Program',
            'Digital Library & Smart Repository Drive',
        ];

        foreach ($campaigns as $name) {
            Campaign::firstOrCreate(['name' => $name]);
        }
    }
}
