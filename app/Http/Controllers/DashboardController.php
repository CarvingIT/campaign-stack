<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\MailQueue;
use App\Models\Newsletter;
use App\Models\OutboundMailAccount;
use App\Models\SentMail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Outbound Mail Accounts
        $mailAccounts = OutboundMailAccount::count();
        $mailAccountList = OutboundMailAccount::orderBy('status', 'desc')->latest()->take(5)->get();
        $activeMailAccounts = OutboundMailAccount::where('status', 1)->count();

        // Contacts & Tags
        $contacts = Contact::count();
        $tagsCount = Tag::count();

        // Campaigns & Newsletters
        $campaigns = Campaign::count();
        $newsletters = Newsletter::count();

        // Email status
        $sentMails = SentMail::count();
        $queuedMails = MailQueue::where('status', 'Q')->count();
        $failedMails = MailQueue::whereNotNull('error')
            ->where('status', 'F')
            ->count();

        // Delivery success rate percentage
        $totalProcessed = $sentMails + $failedMails;
        $deliveryRate = $totalProcessed > 0 ? round(($sentMails / $totalProcessed) * 100, 1) : 100.0;

        // Pipeline Stages
        $draftNewsletters = Newsletter::where('status', 'D')->count();
        $readyNewsletters = Newsletter::where('status', 'N')->count();
        $queuingNewsletters = Newsletter::where('status', 'Q')->count();
        $sentNewsletters = Newsletter::where('status', 'S')->count();

        // Selected end date for analytics chart
        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->startOfDay()
            : now()->startOfDay();

        if ($endDate->gt(now()->startOfDay())) {
            $endDate = now()->startOfDay();
        }

        // Seven day period
        $startDate = $endDate->copy()->subDays(6)->startOfDay();

        // Emails sent for each day
        $emailCounts = [];
        $emailDates = [];
        $emailFullDates = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $emailDates[] = $date->format('M d');
            $emailFullDates[] = $date->format('Y-m-d');
            $emailCounts[] = SentMail::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        // Dynamic Y-axis
        $maxEmailCount = max($emailCounts ?: [0]);
        if ($maxEmailCount <= 0) {
            $yAxisMax = 5;
        } elseif ($maxEmailCount <= 5) {
            $yAxisMax = 5;
        } else {
            $yAxisMax = (int) ceil($maxEmailCount / 5) * 5;
        }

        // Failure codes
        $failureCodes = MailQueue::whereNotNull('error')
            ->where('status', 'F')
            ->whereNotNull('response_code')
            ->selectRaw('response_code, COUNT(*) as count')
            ->groupBy('response_code')
            ->orderBy('response_code')
            ->get();

        // Recent Activity Log
        $recentSent = SentMail::with(['newsletter.campaign', 'contact', 'outbound_mail_account'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($email) {
                return [
                    'subject' => $email->subject,
                    'campaign_name' => $email->newsletter->campaign->name ?? 'Direct Broadcast',
                    'recipient' => $email->contact->email ?? 'Unknown',
                    'sender' => $email->outbound_mail_account->name ?? 'SMTP Gateway',
                    'status' => 'sent',
                    'timestamp' => $email->created_at,
                ];
            });

        $recentQueued = MailQueue::with(['newsletter.campaign', 'contact'])
            ->where('status', 'Q')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($email) {
                return [
                    'subject' => $email->subject,
                    'campaign_name' => $email->newsletter->campaign->name ?? 'Direct Broadcast',
                    'recipient' => $email->contact->email ?? 'Unknown',
                    'sender' => 'Pending Queue',
                    'status' => $email->error ? 'failed' : 'queued',
                    'timestamp' => $email->created_at,
                ];
            });

        $recentActivity = $recentSent->concat($recentQueued)
            ->sortByDesc('timestamp')
            ->take(6)
            ->values();

        // Top Campaigns by Newsletter Count
        $topCampaigns = Campaign::withCount('newsletters')
            ->orderBy('newsletters_count', 'desc')
            ->limit(4)
            ->get();

        // Top Audience Tags with Contact Count
        $topTags = Tag::withCount('contacts')
            ->orderBy('contacts_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'mailAccounts',
            'mailAccountList',
            'activeMailAccounts',
            'contacts',
            'tagsCount',
            'campaigns',
            'newsletters',
            'draftNewsletters',
            'readyNewsletters',
            'queuingNewsletters',
            'sentNewsletters',
            'sentMails',
            'queuedMails',
            'failedMails',
            'deliveryRate',
            'startDate',
            'endDate',
            'emailCounts',
            'emailDates',
            'emailFullDates',
            'maxEmailCount',
            'yAxisMax',
            'failureCodes',
            'recentActivity',
            'topCampaigns',
            'topTags'
        ));
    }
}