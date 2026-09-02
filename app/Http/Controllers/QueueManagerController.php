<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\Contact;
use App\Models\MailQueue;
use App\Models\SentMail;
use App\Models\OutboundMailAccount;
use App\Mail\DynamicDbMail;
use App\Utils\NewsletterTemplateHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class QueueManagerController extends Controller
{
    /**
     * Get live queue statistics.
     */
    public function status()
    {
        $readyNewsletters = Newsletter::where('status', 'N')->count();
        $inQueue = MailQueue::where('status', 'Q')->whereNull('error')->count();
        $failedQueue = MailQueue::where('status', 'Q')->whereNotNull('error')->orWhere('status', 'F')->count();
        $sentToday = SentMail::whereDate('created_at', now()->toDateString())->count();
        $totalSent = SentMail::count();

        return response()->json([
            'success' => true,
            'ready_newsletters' => $readyNewsletters,
            'in_queue' => $inQueue,
            'failed_queue' => $failedQueue,
            'sent_today' => $sentToday,
            'total_sent' => $totalSent,
        ]);
    }

    /**
     * Trigger queue creation for all ready (status 'N') newsletters.
     * Web UI equivalent of 'php artisan CS:queue-mails'.
     */
    public function queueAll()
    {
        $templateHandler = new NewsletterTemplateHandler();
        $newsletters = Newsletter::where('status', 'N')->get();

        if ($newsletters->isEmpty()) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'message' => 'No broadcasts in "Ready (N)" status found. Edit a newsletter to set its status to Ready.',
                'logs' => ['[INFO] No broadcasts in "Ready" status to queue.'],
            ]);
        }

        $totalQueued = 0;
        $logs = [];
        $logs[] = '[START] Scanning ready broadcasts... Found ' . $newsletters->count() . ' newsletter(s).';

        foreach ($newsletters as $n) {
            $n->status = 'Q';
            $n->save();

            $contactIds = collect();

            foreach ($n->newsletter_tags as $newsletterTag) {
                $tag = $newsletterTag->tag;
                if (!$tag) {
                    continue;
                }
                $contactIds = $contactIds->merge($tag->contacts->pluck('id'));
            }

            // Fallback: If no tags are selected, target all active contacts
            if ($n->newsletter_tags->isEmpty()) {
                $contactIds = Contact::pluck('id');
            }

            $contactIds = $contactIds->unique();
            $newsletterQueuedCount = 0;

            foreach ($contactIds as $contactId) {
                $contact = Contact::find($contactId);
                if (!$contact || empty($contact->email)) {
                    continue;
                }

                $mailQueue = new MailQueue;
                $mailQueue->id = (string) Str::uuid();
                $mailQueue->newsletter_id = $n->id;
                $mailQueue->contact_id = $contact->id;
                $mailQueue->status = 'Q';
                $mailQueue->attempt = 0;

                $mailQueue->subject = $templateHandler->process($n->subject_template, $contact);
                $mailQueue->body = $templateHandler->process($n->body_template, $contact);
                $mailQueue->save();

                $newsletterQueuedCount++;
                $totalQueued++;
            }

            $logs[] = "[QUEUED] \"{$n->title}\" → Generated {$newsletterQueuedCount} personalized email(s).";
        }

        $logs[] = "[COMPLETE] Total {$totalQueued} email(s) successfully added to the dispatch queue.";

        return response()->json([
            'success' => true,
            'count' => $totalQueued,
            'message' => "Successfully queued {$totalQueued} email(s) across {$newsletters->count()} broadcast(s).",
            'logs' => $logs,
        ]);
    }

    /**
     * Flush and send queued emails.
     * Web UI equivalent of 'php artisan CS:FlushMailQueue'.
     */
    public function flushQueue(Request $request)
    {
        $limit = (int) $request->input('limit', 20); // Process batch
        $queuedMails = MailQueue::whereIn('status', ['Q', 'N'])
            ->with(['contact', 'newsletter.outbound_mail_accounts'])
            ->limit($limit)
            ->get();

        if ($queuedMails->isEmpty()) {
            return response()->json([
                'success' => true,
                'sent_count' => 0,
                'failed_count' => 0,
                'remaining' => 0,
                'message' => 'Dispatch queue is empty. No emails to send.',
                'logs' => ['[INFO] Queue is empty.'],
            ]);
        }

        $sentCount = 0;
        $failedCount = 0;
        $logs = [];
        $logs[] = "[DISPATCH START] Processing batch of " . $queuedMails->count() . " email(s)...";

        foreach ($queuedMails as $q_m) {
            if (!$q_m->contact || !$q_m->newsletter) {
                $q_m->delete();
                continue;
            }

            $mail_accounts = $q_m->newsletter->outbound_mail_accounts ?? collect();
            $active_m_a = null;

            foreach ($mail_accounts as $m_a) {
                $isActiveDate = empty($m_a->active_after) || (new \DateTime() >= new \DateTime($m_a->active_after));
                if ($isActiveDate && (int)$m_a->status !== 0) {
                    $active_m_a = $m_a;
                    break;
                }
            }

            // Fallback to first active outbound mail account in system
            if (!$active_m_a) {
                $active_m_a = OutboundMailAccount::where(function ($query) {
                    $query->where('status', '!=', 0)->orWhereNull('status');
                })->orderBy('id', 'asc')->first();
            }

            try {
                if (!$active_m_a) {
                    throw new \Exception("No active outbound mail account configured.");
                }

                $rawConfig = json_decode($active_m_a->config, true) ?: [];

                $fromAddress = !empty($rawConfig['from_address']) 
                    ? $rawConfig['from_address'] 
                    : (!empty($rawConfig['username']) && filter_var($rawConfig['username'], FILTER_VALIDATE_EMAIL) 
                        ? $rawConfig['username'] 
                        : config('mail.from.address', 'noreply@campaignstack.in'));

                $fromName = !empty($rawConfig['from_username']) 
                    ? $rawConfig['from_username'] 
                    : 'Campaign Stack';

                $mailConfig = [
                    'transport' => 'smtp',
                    'host' => $rawConfig['ip_address'] ?? $rawConfig['host'] ?? '127.0.0.1',
                    'port' => (int) ($rawConfig['port'] ?? 587),
                    'encryption' => (!empty($rawConfig['encryption']) && strtolower($rawConfig['encryption']) !== 'none') 
                        ? strtolower($rawConfig['encryption']) 
                        : null,
                    'username' => $rawConfig['username'] ?? null,
                    'password' => $rawConfig['password'] ?? null,
                    'timeout' => 15,
                    'from' => [
                        'address' => $fromAddress,
                        'name' => $fromName,
                    ],
                ];

                $mailable = new DynamicDbMail($q_m->subject, $q_m->body, $fromAddress, $fromName);
                $mailer = Mail::build($mailConfig);
                $mailer->to($q_m->contact->email)->send($mailable);

                // On success, save to sent_mails
                $sent_mail = new SentMail;
                $sent_mail->id = (string) Str::uuid();
                $sent_mail->newsletter_id = $q_m->newsletter_id;
                $sent_mail->outbound_mail_account_id = $active_m_a->id;
                $sent_mail->contact_id = $q_m->contact_id;
                $sent_mail->subject = $q_m->subject;
                $sent_mail->body = $q_m->body;
                $sent_mail->opened = 0;
                $sent_mail->save();

                $q_m->delete();
                $sentCount++;
                $logs[] = "[SUCCESS] Dispatched to {$q_m->contact->email} via {$active_m_a->name}";
            } catch (\Exception $e) {
                $attempt = (int) $q_m->attempt + 1;
                $q_m->attempt = $attempt;
                $q_m->sending_attempted_at = now();
                $q_m->response_code = is_numeric($e->getCode()) && $e->getCode() != 0 ? (int) $e->getCode() : 500;
                $q_m->error = substr($e->getMessage(), 0, 250);
                $q_m->save();

                $failedCount++;
                $logs[] = "[FAILED] {$q_m->contact->email}: " . substr($e->getMessage(), 0, 100);
            }
        }

        $remaining = MailQueue::whereIn('status', ['Q', 'N'])->whereNull('error')->count();
        $logs[] = "[BATCH FINISHED] Sent: {$sentCount}, Failed: {$failedCount}, Remaining in queue: {$remaining}";

        // Update newsletter status to 'S' (Sent) if all queue entries for it are finished
        $activeNewsletters = Newsletter::where('status', 'Q')->get();
        foreach ($activeNewsletters as $n) {
            $pendingForN = MailQueue::where('newsletter_id', $n->id)->count();
            if ($pendingForN === 0) {
                $n->status = 'S';
                $n->save();
                $logs[] = "[BROADCAST COMPLETED] \"{$n->title}\" marked as Sent.";
            }
        }

        return response()->json([
            'success' => true,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'remaining' => $remaining,
            'message' => "Batch completed. Sent: {$sentCount}, Failed: {$failedCount}.",
            'logs' => $logs,
        ]);
    }

    /**
     * Retry all failed queue emails.
     */
    public function retryFailed()
    {
        $count = MailQueue::whereNotNull('error')->update([
            'status' => 'Q',
            'error' => null,
            'response_code' => null,
            'attempt' => 0,
        ]);

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "Reset {$count} failed email(s) back to active queue.",
            'logs' => ["[RETRY] Re-queued {$count} failed emails for delivery."],
        ]);
    }

    /**
     * Clear the entire mail queue.
     */
    public function clearQueue()
    {
        $count = MailQueue::count();
        MailQueue::truncate();

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "Cleared {$count} item(s) from the mail queue.",
            'logs' => ["[CLEARED] Wiped {$count} emails from dispatch queue."],
        ]);
    }

    /**
     * Queue a specific single newsletter.
     */
    public function queueSingle($id)
    {
        $newsletter = Newsletter::findOrFail($id);
        $templateHandler = new NewsletterTemplateHandler();

        $newsletter->status = 'Q';
        $newsletter->save();

        $contactIds = collect();
        foreach ($newsletter->newsletter_tags as $newsletterTag) {
            if ($tag = $newsletterTag->tag) {
                $contactIds = $contactIds->merge($tag->contacts->pluck('id'));
            }
        }

        if ($newsletter->newsletter_tags->isEmpty()) {
            $contactIds = Contact::pluck('id');
        }

        $contactIds = $contactIds->unique();
        $totalQueued = 0;

        foreach ($contactIds as $contactId) {
            $contact = Contact::find($contactId);
            if (!$contact || empty($contact->email)) {
                continue;
            }

            $mailQueue = new MailQueue;
            $mailQueue->id = (string) Str::uuid();
            $mailQueue->newsletter_id = $newsletter->id;
            $mailQueue->contact_id = $contact->id;
            $mailQueue->status = 'Q';
            $mailQueue->attempt = 0;
            $mailQueue->subject = $templateHandler->process($newsletter->subject_template, $contact);
            $mailQueue->body = $templateHandler->process($newsletter->body_template, $contact);
            $mailQueue->save();
            $totalQueued++;
        }

        return response()->json([
            'success' => true,
            'count' => $totalQueued,
            'message' => "Queued {$totalQueued} email(s) for \"{$newsletter->title}\".",
        ]);
    }
}
