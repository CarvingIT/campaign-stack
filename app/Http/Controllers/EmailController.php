<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailController extends Controller
{
    public function list()
    {
        return view('emailsmanagement');
    }

    public function data(Request $request)
    {
        $sent = DB::table('sent_mails as sm')
            ->leftJoin('newsletters as n', 'sm.newsletter_id', '=', 'n.id')
            ->leftJoin('campaigns as c', 'n.campaign_id', '=', 'c.id')
            ->leftJoin('contacts as ct', 'sm.contact_id', '=', 'ct.id')
            ->leftJoin(
                'outbound_mail_accounts as oma',
                'sm.outbound_mail_account_id',
                '=',
                'oma.id'
            )
            ->select(
                'sm.subject',
                'c.name as campaign_name',
                'ct.email as recipient',
                'oma.name as sender_mail_account',
                'sm.created_at as timestamp'
            )
            ->selectRaw("'sent' as email_status");

        $queued = DB::table('mail_queues as mq')
            ->leftJoin('newsletters as n', 'mq.newsletter_id', '=', 'n.id')
            ->leftJoin('campaigns as c', 'n.campaign_id', '=', 'c.id')
            ->leftJoin('contacts as ct', 'mq.contact_id', '=', 'ct.id')
            ->leftJoin(
                'outbound_mail_accounts as oma',
                'mq.outbound_mail_account_id',
                '=',
                'oma.id'
            )
            ->where('mq.status', 'Q')
            ->whereNull('mq.error')
            ->select(
                'mq.subject',
                'c.name as campaign_name',
                'ct.email as recipient',
                DB::raw("COALESCE(oma.name, 'Not assigned yet') as sender_mail_account"),
                'mq.created_at as timestamp'
            )
            ->selectRaw("'queued' as email_status");

        $failed = DB::table('mail_queues as mq')
            ->leftJoin('newsletters as n', 'mq.newsletter_id', '=', 'n.id')
            ->leftJoin('campaigns as c', 'n.campaign_id', '=', 'c.id')
            ->leftJoin('contacts as ct', 'mq.contact_id', '=', 'ct.id')
            ->leftJoin(
                'outbound_mail_accounts as oma',
                'mq.outbound_mail_account_id',
                '=',
                'oma.id'
            )
            ->where('mq.status', 'Q')
            ->whereNotNull('mq.error')
            ->select(
                'mq.subject',
                'c.name as campaign_name',
                'ct.email as recipient',
                DB::raw("COALESCE(oma.name, 'N/A') as sender_mail_account"),
                'mq.created_at as timestamp'
            )
            ->selectRaw("'failed' as email_status");

        $query = DB::query()
            ->fromSub(
                $sent
                    ->unionAll($queued)
                    ->unionAll($failed),
                'emails'
            );

        // Status filter
        $status = $request->input('status', 'all');

        if (in_array($status, ['sent', 'queued', 'failed'], true)) {
            $query->where('email_status', $status);
        }

        // Total records
        $recordsTotal = (clone $query)->count();

        // Search
        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('campaign_name', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%")
                    ->orWhere('sender_mail_account', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        // Latest first
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
}