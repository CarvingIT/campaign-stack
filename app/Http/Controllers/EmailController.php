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

        /*
         * Sent emails
         */
        $sent = SentMail::query()
            ->leftJoin('newsletters as n', 'sent_mails.newsletter_id', '=', 'n.id')
            ->leftJoin('campaigns as c', 'n.campaign_id', '=', 'c.id')
            ->leftJoin('contacts as ct', 'sent_mails.contact_id', '=', 'ct.id')
            ->leftJoin(
                'outbound_mail_accounts as oma',
                'sent_mails.outbound_mail_account_id',
                '=',
                'oma.id'
            )
            ->select(
                'sent_mails.subject',
                'c.name as campaign_name',
                'ct.email as recipient',
                'oma.name as sender_mail_account',
                'sent_mails.created_at as timestamp'
            )
            ->selectRaw("'sent' as email_status");

        /*
         * Queued and failed emails
         *
         * The outbound mail account is NOT selected here because
         * the account is decided when the email is actually sent.
         */
        $queued = MailQueue::query()
            ->leftJoin('newsletters as n', 'mail_queues.newsletter_id', '=', 'n.id')
            ->leftJoin('campaigns as c', 'n.campaign_id', '=', 'c.id')
            ->leftJoin('contacts as ct', 'mail_queues.contact_id', '=', 'ct.id')
            ->where('mail_queues.status', 'Q')
            ->whereNull('mail_queues.error')
            ->select(
                'mail_queues.subject',
                'c.name as campaign_name',
                'ct.email as recipient'
            )
            ->selectRaw("'Not assigned yet' as sender_mail_account")
            ->selectRaw("mail_queues.created_at as timestamp")
            ->selectRaw("'queued' as email_status");

        $failed = MailQueue::query()
            ->leftJoin('newsletters as n', 'mail_queues.newsletter_id', '=', 'n.id')
            ->leftJoin('campaigns as c', 'n.campaign_id', '=', 'c.id')
            ->leftJoin('contacts as ct', 'mail_queues.contact_id', '=', 'ct.id')
            ->where('mail_queues.status', 'Q')
            ->whereNotNull('mail_queues.error')
            ->select(
                'mail_queues.subject',
                'c.name as campaign_name',
                'ct.email as recipient'
            )
            ->selectRaw("'Not assigned yet' as sender_mail_account")
            ->selectRaw("mail_queues.created_at as timestamp")
            ->selectRaw("'failed' as email_status");

        /*
         * Select the required email source based on the filter.
         */
        if ($status === 'sent') {
            $query = $sent;
        } elseif ($status === 'queued') {
            $query = $queued;
        } elseif ($status === 'failed') {
            $query = $failed;
        } else {
            $query = $sent
                ->unionAll($queued)
                ->unionAll($failed);
        }

        /*
         * Total records before search.
         */
        $recordsTotal = $this->countQuery($query);

        /*
         * DataTables search.
         */
        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('campaign_name', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%")
                    ->orWhere('sender_mail_account', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $this->countQuery($query);

        /*
         * Latest emails first + server-side pagination.
         */
        $data = $query
            ->orderBy('timestamp', 'desc')
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    private function countQuery($query)
    {
        return $query->count();
    }
}