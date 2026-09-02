<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Newsletter;
use App\Models\Tag;
use App\Models\OutboundMailAccount;
use App\Models\NewsletterTag;
use App\Models\NewsletterOutboundMailAccount;
use Illuminate\Support\Str;

class NewsletterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = Campaign::all()->keyBy('name');
        $tags = Tag::all()->keyBy('label');
        $accounts = OutboundMailAccount::all()->keyBy('name');

        $newslettersData = [
            [
                'title' => 'Koha LMS Enterprise 2026 Rollout - Library Directors',
                'campaign_name' => 'Koha LMS Enterprise Upgrade 2026',
                'subject_template' => 'Exclusive Upgrade: Modernize {{ company | Your Institution }} Library with Koha 2026 Cloud',
                'body_template' => '<p>Dear {{ salutation | Dr./Prof. }} {{ lastname | Colleague }},</p><p>We are excited to share that next-generation cloud hosting and automation for Koha LMS is now live for {{ company | your university }}.</p><p>Would you have 15 minutes this Thursday for a brief walkthrough with our solutions team?</p><p>Warm regards,<br><strong>Campaign Stack Team</strong><br>Bengaluru, India</p>',
                'status' => 'N', // Ready for CS:queue-mails
                'tags' => ['Academic & Library Directors', 'Koha LMS Users - India', 'IIT & NIT Institutional Contacts'],
                'accounts' => ['AWS SES Mumbai (Primary Relay)', 'Zoho Mail India SMTP'],
            ],
            [
                'title' => 'Executive Briefing: Cloud Security & Data Compliance Summit',
                'campaign_name' => 'Cloud Security & Compliance Summit - Bangalore',
                'subject_template' => 'Securing Cloud Workloads at {{ company | your organization }} - Summit Invite',
                'body_template' => '<p>Hello {{ firstname | there }},</p><p>As {{ company | your enterprise }} scales its digital operations, ensuring zero-trust security and DPDP compliance is paramount.</p><p>We are hosting an exclusive roundtable in Bangalore next month. Reply to reserve a delegate pass for your engineering leadership.</p><p>Best,<br><strong>Arjun Sharma</strong><br>Head of Partnerships</p>',
                'status' => 'N', // Ready for CS:queue-mails
                'tags' => ['IT & Infrastructure Heads - Mumbai/BLR', 'Enterprise Procurement'],
                'accounts' => ['Google Workspace SMTP Relay', 'AWS SES Mumbai (Primary Relay)'],
            ],
            [
                'title' => 'Early Access: Cold Outreach Automation for Indian Tech Teams',
                'campaign_name' => 'AI Automation & Workflow Pilot Program',
                'subject_template' => '{{ firstname | Founder }}, streamline cold outreach for {{ company | your team }}',
                'body_template' => '<p>Hi {{ firstname | Friend }},</p><p>Cold email deliverability is tough when managing multiple SMTP pools. Campaign Stack helps {{ company | fast-growing tech teams }} automate warmup, rotation, and analytics effortlessly.</p><p>Check out our interactive pilot sandbox today.</p><p>Cheers,<br><strong>The Campaign Stack Engineering Team</strong></p>',
                'status' => 'N', // Ready for CS:queue-mails
                'tags' => ['CTO & Tech Leaders - India', 'SaaS & Startup Founders'],
                'accounts' => ['SendGrid Marketing Gateway', 'AWS SES Mumbai (Primary Relay)'],
            ],
            [
                'title' => 'Q3 SaaS Growth Masterclass: High Deliverability Outreach',
                'campaign_name' => 'Q3 SaaS Growth Outreach - India',
                'subject_template' => 'Boosting lead conversion at {{ company | your company }} this quarter',
                'body_template' => '<p>Hi {{ firstname | Marketer }},</p><p>We analyzed cold outreach campaigns across 200+ Indian B2B tech companies. The top insight: domain warmup and multi-SMTP rotation improves inbox placement by 42%.</p><p>Let us know if you would like the benchmark deck for {{ company | your team }}.</p><p>Best regards,<br><strong>Priya Nair</strong></p>',
                'status' => 'N', // Ready for CS:queue-mails
                'tags' => ['Marketing & Growth Leads', 'Fintech Product Managers'],
                'accounts' => ['AWS SES Mumbai (Primary Relay)', 'Google Workspace SMTP Relay'],
            ],
            [
                'title' => 'Draft: Digital Library & Smart Repository Drive 2026',
                'campaign_name' => 'Digital Library & Smart Repository Drive',
                'subject_template' => 'Transforming institutional archives at {{ company | your institution }}',
                'body_template' => '<p>Dear {{ salutation | Professor }} {{ lastname | Educator }},</p><p>Discover our institutional repository digitalization toolkit tailored for Indian universities.</p>',
                'status' => 'D', // Draft
                'tags' => ['Academic & Library Directors'],
                'accounts' => ['AWS SES Mumbai (Primary Relay)'],
            ],
        ];

        foreach ($newslettersData as $item) {
            $campaign = $campaigns->get($item['campaign_name']);
            if (!$campaign) {
                continue;
            }

            $newsletter = Newsletter::firstOrCreate(
                ['title' => $item['title']],
                [
                    'uuid' => (string) Str::uuid(),
                    'campaign_id' => $campaign->id,
                    'subject_template' => $item['subject_template'],
                    'body_template' => $item['body_template'],
                    'status' => $item['status'],
                ]
            );

            // Attach tags
            $tagIds = [];
            foreach ($item['tags'] as $tagName) {
                if ($tag = $tags->get($tagName)) {
                    $tagIds[] = $tag->id;
                    NewsletterTag::firstOrCreate([
                        'newsletter_id' => $newsletter->id,
                        'tag_id' => $tag->id,
                    ]);
                }
            }

            // Attach outbound mail accounts
            $priority = 1;
            foreach ($item['accounts'] as $accName) {
                if ($acc = $accounts->get($accName)) {
                    NewsletterOutboundMailAccount::firstOrCreate(
                        [
                            'newsletter_id' => $newsletter->id,
                            'outbound_mail_account_id' => $acc->id,
                        ],
                        [
                            'priority' => $priority++,
                        ]
                    );
                }
            }
        }
    }
}
