<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SentMailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Left empty intentionally: Sent mails should only be created when CS:FlushMailQueue executes.
    }
}
