<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MailQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Left empty intentionally: MailQueue entries should only be created when CS:queue-mails executes.
    }
}
