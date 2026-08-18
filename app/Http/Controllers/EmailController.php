<?php

namespace App\Http\Controllers;

use App\Models\MailQueue;
use App\Models\SentMail;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function list()
    {
        return view('emailsmanagement');
    }

    public function data(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search.value');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($status === 'sent') {
            $query = SentMail::with([
                'newsletter.campaign',
                'contact',
                'outbound_mail_account'
            ]);

            $this->applySearch($query, $search);

            $recordsTotal = SentMail::count();
            $recordsFiltered = $query->count();

            $emails = $query
                ->orderBy('created_at', 'desc')
                ->offset($start)
                ->limit($length)
                ->get();

        } else {
            $query = MailQueue::with([
                'newsletter.campaign',
                'contact'
            ]);

            if ($status === 'queued') {
                $query->where('status', 'Q')
                    ->whereNull('error');
            } elseif ($status === 'failed') {
                $query->where('status', 'Q')
                    ->whereNotNull('error');
            }

            $this->applySearch($query, $search);

            if ($status === 'queued') {
                $recordsTotal = MailQueue::where('status', 'Q')
                    ->whereNull('error')
                    ->count();
            } elseif ($status === 'failed') {
                $recordsTotal = MailQueue::where('status', 'Q')
                    ->whereNotNull('error')
                    ->count();
            } else {
                $recordsTotal = SentMail::count()
                    + MailQueue::where('status', 'Q')->count();
            }

            $recordsFiltered = $query->count();

            if ($status === 'all') {
                $sentQuery = SentMail::with([
                    'newsletter.campaign',
                    'contact',
                    'outbound_mail_account'
                ]);

                $queueQuery = MailQueue::with([
                    'newsletter.campaign',
                    'contact'
                ])->where('status', 'Q');

                $this->applySearch($sentQuery, $search);
                $this->applySearch($queueQuery, $search);

                $sentCount = $sentQuery->count();
                $queueCount = $queueQuery->count();

                $recordsTotal = SentMail::count()
                    + MailQueue::where('status', 'Q')->count();

                $recordsFiltered = $sentCount + $queueCount;

                $emails = $sentQuery
                    ->orderBy('created_at', 'desc')
                    ->limit($start + $length)
                    ->get()
                    ->map(function ($email) {
                        return $this->formatSentEmail($email);
                    });

                $queuedEmails = $queueQuery
                    ->orderBy('created_at', 'desc')
                    ->limit($start + $length)
                    ->get()
                    ->map(function ($email) {
                        return $this->formatQueuedEmail($email);
                    });

                $emails = $emails
                    ->concat($queuedEmails)
                    ->sortByDesc('timestamp')
                    ->slice($start, $length)
                    ->values();
            } else {
                $emails = $emails->map(function ($email) {
                    return $this->formatQueuedEmail($email);
                });
            }
        }

        if ($status === 'sent') {
            $emails = $emails->map(function ($email) {
                return $this->formatSentEmail($email);
            });
        }

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $emails,
        ]);
    }

    private function applySearch($query, $search)
    {
        if (empty($search)) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%{$search}%")
                ->orWhereHas('newsletter', function ($newsletter) use ($search) {
                    $newsletter->whereHas('campaign', function ($campaign) use ($search) {
                        $campaign->where('name', 'like', "%{$search}%");
                    });
                })
                ->orWhereHas('contact', function ($contact) use ($search) {
                    $contact->where('email', 'like', "%{$search}%");
                });
        });
    }

    private function formatSentEmail($email)
    {
        return [
            'subject' => $email->subject,
            'campaign_name' => $email->newsletter->campaign->name ?? '',
            'recipient' => $email->contact->email ?? '',
            'sender_mail_account' => $email->outbound_mail_account->name ?? '',
            'timestamp' => $email->created_at,
        ];
    }

    private function formatQueuedEmail($email)
    {
        return [
            'subject' => $email->subject,
            'campaign_name' => $email->newsletter->campaign->name ?? '',
            'recipient' => $email->contact->email ?? '',
            'sender_mail_account' => 'Not assigned yet',
            'timestamp' => $email->created_at,
        ];
    }
}