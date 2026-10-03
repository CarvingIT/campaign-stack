<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OutboundMailAccount;

class OutboundMailAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'AWS SES Mumbai (Primary Relay)',
                'type' => 'SMTP',
                'status' => 1,
                'active_after' => now()->subDays(30),
                'config' => json_encode([
                    'username' => 'AKIAIOSFODNN7EXAMPLE',
                    'password' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
                    'ip_address' => 'email-smtp.ap-south-1.amazonaws.com',
                    'port' => '587',
                    'encryption' => 'tls',
                    'from_username' => 'Campaign Stack Growth',
                    'from_address' => 'growth@campaignstack.in',
                ]),
            ],
            [
                'name' => 'Google Workspace SMTP Relay',
                'type' => 'SMTP',
                'status' => 1,
                'active_after' => now()->subDays(15),
                'config' => json_encode([
                    'username' => 'outreach@yourdomain.in',
                    'password' => 'your-app-specific-password',
                    'ip_address' => 'smtp.gmail.com',
                    'port' => '587',
                    'encryption' => 'tls',
                    'from_username' => 'Arjun from CampaignStack',
                    'from_address' => 'outreach@yourdomain.in',
                ]),
            ],
            [
                'name' => 'SendGrid Marketing Gateway',
                'type' => 'SMTP',
                'status' => 1,
                'active_after' => now()->subDays(10),
                'config' => json_encode([
                    'username' => 'apikey',
                    'password' => 'SG.your_sendgrid_api_key_placeholder',
                    'ip_address' => 'smtp.sendgrid.net',
                    'port' => '587',
                    'encryption' => 'tls',
                    'from_username' => 'Product Updates',
                    'from_address' => 'updates@campaignstack.in',
                ]),
            ],
            [
                'name' => 'Zoho Mail India SMTP',
                'type' => 'SMTP',
                'status' => 1,
                'active_after' => now()->subDays(5),
                'config' => json_encode([
                    'username' => 'contact@yourdomain.in',
                    'password' => 'your-zoho-password-placeholder',
                    'ip_address' => 'smtppro.zoho.in',
                    'port' => '465',
                    'encryption' => 'ssl',
                    'from_username' => 'Priya Nair',
                    'from_address' => 'contact@yourdomain.in',
                ]),
            ],
        ];

        foreach ($accounts as $accountData) {
            OutboundMailAccount::firstOrCreate(
                ['name' => $accountData['name']],
                $accountData
            );
        }
    }
}
