<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'CTO & Tech Leaders - India',
            'SaaS & Startup Founders',
            'Fintech Product Managers',
            'IT & Infrastructure Heads - Mumbai/BLR',
            'Academic & Library Directors',
            'HR & People Operations',
            'Marketing & Growth Leads',
            'Enterprise Procurement',
            'Koha LMS Users - India',
            'IIT & NIT Institutional Contacts',
        ];

        foreach ($tags as $tagLabel) {
            Tag::firstOrCreate(['label' => $tagLabel]);
        }
    }
}
