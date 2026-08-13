<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
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
        // Dashboard statistics
        $mailAccounts = OutboundMailAccount::count();
        $contacts = Contact::count();
        $campaigns = Campaign::count();
        $newsletters = Newsletter::count();

        // Email status
        $sentMails = SentMail::count();

        $queuedMails = MailQueue::where('status', 'Q')
            ->count();

        $failedMails = MailQueue::whereNotNull('error')
            ->where('status', 'F')
            ->count();

        // Selected end date
        $endDate = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))->startOfDay()
            : now()->startOfDay();

        // Prevent future dates
        if ($endDate->gt(now()->startOfDay())) {
            $endDate = now()->startOfDay();
        }

        // Seven day period
        $startDate = $endDate->copy()
            ->subDays(6)
            ->startOfDay();

        // Emails sent for each day
        $emailCounts = [];
        $emailDates = [];
        $emailFullDates = [];

        for (
            $date = $startDate->copy();
            $date->lte($endDate);
            $date->addDay()
        ) {
            $emailDates[] = $date->format('M d');
            $emailFullDates[] = $date->format('Y-m-d');

            $emailCounts[] = SentMail::whereDate(
                'created_at',
                $date->format('Y-m-d')
            )->count();
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

        return view('dashboard', compact(
            'mailAccounts',
            'contacts',
            'campaigns',
            'newsletters',
            'sentMails',
            'queuedMails',
            'failedMails',
            'startDate',
            'endDate',
            'emailCounts',
            'emailDates',
            'emailFullDates',
            'yAxisMax',
            'failureCodes'
        ));
    }
}