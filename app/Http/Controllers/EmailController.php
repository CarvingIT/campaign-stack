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
                $emails = $query
                    ->orderBy('created_at', 'desc')
                    ->offset($start)
                    ->limit($length)
                    ->get()
                    ->map(function ($email) {
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
            'id' => $email->id,
            'type' => 'sent',
            'status' => 'sent',
            'subject' => $email->subject,
            'campaign_name' => $email->newsletter->campaign->name ?? 'Direct Broadcast',
            'recipient' => $email->contact->email ?? '',
            'recipient_name' => trim(($email->contact->firstname ?? '') . ' ' . ($email->contact->lastname ?? '')),
            'recipient_company' => $email->contact->company ?? '',
            'sender_mail_account' => $email->outbound_mail_account->name ?? 'SMTP Server',
            'timestamp' => $email->created_at,
            'opened' => (bool) $email->opened,
        ];
    }

    private function formatQueuedEmail($email)
    {
        $isFailed = !empty($email->error) || $email->status === 'F';
        return [
            'id' => $email->id,
            'type' => $isFailed ? 'failed' : 'queued',
            'status' => $isFailed ? 'failed' : 'queued',
            'subject' => $email->subject,
            'campaign_name' => $email->newsletter->campaign->name ?? 'Direct Broadcast',
            'recipient' => $email->contact->email ?? '',
            'recipient_name' => trim(($email->contact->firstname ?? '') . ' ' . ($email->contact->lastname ?? '')),
            'recipient_company' => $email->contact->company ?? '',
            'sender_mail_account' => $isFailed ? 'Failed Delivery' : 'Queued (Pending Dispatch)',
            'timestamp' => $email->created_at,
            'attempt' => $email->attempt ?? 0,
            'response_code' => $email->response_code,
            'error' => $email->error,
        ];
    }

    public function details($id)
    {
        // Try SentMail first
        $sent = SentMail::with(['newsletter.campaign', 'contact', 'outbound_mail_account'])->find($id);
        if ($sent) {
            return response()->json([
                'success' => true,
                'id' => $sent->id,
                'type' => 'sent',
                'status' => 'sent',
                'subject' => $sent->subject,
                'body' => $sent->body,
                'recipient' => $sent->contact->email ?? '',
                'recipient_name' => trim(($sent->contact->salutation ?? '') . ' ' . ($sent->contact->firstname ?? '') . ' ' . ($sent->contact->lastname ?? '')),
                'recipient_company' => $sent->contact->company ?? '',
                'recipient_phone' => $sent->contact->mobile ?? '',
                'campaign_name' => $sent->newsletter->campaign->name ?? 'Direct Broadcast',
                'newsletter_title' => $sent->newsletter->title ?? '',
                'sender' => $sent->outbound_mail_account->name ?? 'Default SMTP Gateway',
                'timestamp' => $sent->created_at->format('M d, Y h:i:s A'),
                'opened' => (bool) $sent->opened,
            ]);
        }

        // Try MailQueue
        $queued = MailQueue::with(['newsletter.campaign', 'contact'])->find($id);
        if ($queued) {
            $isFailed = !empty($queued->error) || $queued->status === 'F';
            return response()->json([
                'success' => true,
                'id' => $queued->id,
                'type' => $isFailed ? 'failed' : 'queued',
                'status' => $isFailed ? 'failed' : 'queued',
                'subject' => $queued->subject,
                'body' => $queued->body,
                'recipient' => $queued->contact->email ?? '',
                'recipient_name' => trim(($queued->contact->salutation ?? '') . ' ' . ($queued->contact->firstname ?? '') . ' ' . ($queued->contact->lastname ?? '')),
                'recipient_company' => $queued->contact->company ?? '',
                'recipient_phone' => $queued->contact->mobile ?? '',
                'campaign_name' => $queued->newsletter->campaign->name ?? 'Direct Broadcast',
                'newsletter_title' => $queued->newsletter->title ?? '',
                'sender' => 'Pending Dispatch Engine',
                'timestamp' => $queued->created_at->format('M d, Y h:i:s A'),
                'attempt' => $queued->attempt ?? 0,
                'response_code' => $queued->response_code,
                'error' => $queued->error,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Email record not found.'], 404);
    }
}