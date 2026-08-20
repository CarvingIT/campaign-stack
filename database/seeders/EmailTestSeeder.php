<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\MailQueue;
use App\Models\Newsletter;
use App\Models\OutboundMailAccount;
use App\Models\SentMail;
use Illuminate\Database\Seeder;

class EmailTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create test campaigns
        $campaigns = Campaign::factory()->count(3)->create();

        // Create test newsletters linked to campaigns
        $newsletters = $campaigns->map(function ($campaign) {
            return Newsletter::factory()->create([
                'campaign_id' => $campaign->id,
            ]);
        });

        // Create test contacts
        $contacts = Contact::factory()->count(10)->create();

        // Create fake sender mail accounts
        $mailAccounts = OutboundMailAccount::factory()->count(2)->create();

        // Create sent emails
        SentMail::factory()->count(20)->create([
            'newsletter_id' => $newsletters->random()->id,
            'contact_id' => $contacts->random()->id,
            'outbound_mail_account_id' => $mailAccounts->random()->id,
        ]);

        // Create queued emails
        MailQueue::factory()->count(20)->create([
            'newsletter_id' => $newsletters->random()->id,
            'contact_id' => $contacts->random()->id,
        ]);

        // Create failed emails
        MailQueue::factory()->count(10)->create([
            'newsletter_id' => $newsletters->random()->id,
            'contact_id' => $contacts->random()->id,
            'status' => 'Q',
            'attempt' => 1,
            'response_code' => 500,
            'error' => 'Test email sending failure',
            'sending_attempted_at' => now(),
        ]);
    }
}