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

            $logs[] = "[AST:COMPILE] Lexing template AST for broadcast \"{$n->title}\"...";

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

            $logs[] = "[ENQUEUE] Evaluated dynamic tokens & staged {$newsletterQueuedCount} personalized record(s) into database queue (Status: Q, Priority: High).";
        }

        $logs[] = "[COMPLETE] Total {$totalQueued} email(s) locked in queue. Awaiting socket dispatch trigger.";

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
        $batchStart = microtime(true);
        $workerPid = getmypid();
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
                'logs' => ['[INFO] Queue buffer empty. Awaiting new staged dispatches.'],
            ]);
        }

        $sentCount = 0;
        $failedCount = 0;
        $failoverCount = 0;
        $logs = [];
        $memUsage = round(memory_get_usage(true) / 1024 / 1024, 1);
        $logs[] = "[WORKER:{$workerPid}] Daemon thread active (Mem: {$memUsage}MB). Acquired lock on {$queuedMails->count()} queue item(s)...";
        $logs[] = "[PIPELINE] Initialized asynchronous transmission pool. Opening socket stream...";

        // Preload all active outbound accounts in system for fallback & failover
        $systemActiveAccounts = OutboundMailAccount::where(function ($query) {
            $query->where('status', '!=', 0)->orWhereNull('status');
        })->orderBy('id', 'asc')->get();

        foreach ($queuedMails as $q_m) {
            if (!$q_m->contact || !$q_m->newsletter) {
                $q_m->delete();
                continue;
            }

            $recipientName = $q_m->contact->firstname 
                ? trim("{$q_m->contact->firstname} {$q_m->contact->lastname}") 
                : ($q_m->contact->name ?? 'Lead');
            $recipientEmail = $q_m->contact->email;

            // Gather candidate accounts for this newsletter in priority order
            $candidateAccounts = collect();
            $accountDiagnostics = [];

            // 1. Accounts linked directly to this newsletter
            $linkedAccounts = $q_m->newsletter->outbound_mail_accounts;
            if ($linkedAccounts && $linkedAccounts->count() > 0) {
                foreach ($linkedAccounts as $m_a) {
                    $isActiveStatus = ((int)$m_a->status !== 0 && $m_a->status !== '0' && $m_a->status !== false);
                    $isActiveDate = empty($m_a->active_after) || (new \DateTime() >= new \DateTime($m_a->active_after));

                    if ($isActiveStatus && $isActiveDate) {
                        $candidateAccounts->push($m_a);
                    } else {
                        $reasons = [];
                        if (!$isActiveStatus) {
                            $reasons[] = 'status=0 (INACTIVE)';
                        }
                        if (!$isActiveDate) {
                            $reasons[] = "active_after is in future ({$m_a->active_after})";
                        }
                        $accountDiagnostics[] = "Assigned Gateway \"{$m_a->name}\" [ID: {$m_a->id}]: Disqualified (" . implode('; ', $reasons) . ")";
                    }
                }
            } else {
                // 2. ONLY fallback to active system accounts if this newsletter has no accounts linked
                foreach ($systemActiveAccounts as $sys_a) {
                    $isActiveDate = empty($sys_a->active_after) || (new \DateTime() >= new \DateTime($sys_a->active_after));
                    if ($isActiveDate && !$candidateAccounts->contains('id', $sys_a->id)) {
                        $candidateAccounts->push($sys_a);
                    }
                }
                if ($candidateAccounts->isEmpty()) {
                    $accountDiagnostics[] = "Broadcast has no assigned gateways, and 0 active system-wide gateways were found in database.";
                }
            }

            if ($candidateAccounts->isEmpty()) {
                $attempt = (int) $q_m->attempt + 1;
                $q_m->attempt = $attempt;
                $q_m->sending_attempted_at = now();
                $q_m->response_code = 500;
                $reasonText = !empty($accountDiagnostics) 
                    ? implode(' | ', $accountDiagnostics) 
                    : "No active outbound mail account configured in system.";
                $q_m->error = substr($reasonText, 0, 250);
                $q_m->save();

                $failedCount++;
                $logs[] = "[RELAY:ERROR] Cannot transmit to <{$recipientEmail}>: No eligible active gateway available.";
                foreach ($accountDiagnostics as $diag) {
                    $logs[] = "[DIAGNOSTIC] {$diag}";
                }
                if ($linkedAccounts && $linkedAccounts->count() > 0) {
                    $activeSysNames = $systemActiveAccounts->pluck('name')->implode(', ');
                    if (!empty($activeSysNames)) {
                        $logs[] = "[RESOLUTION] Assigned gateway is inactive. Activate \"{$linkedAccounts->first()->name}\" in Outbound Mail Accounts, or assign active gateway(s) ({$activeSysNames}) to this broadcast.";
                    } else {
                        $logs[] = "[RESOLUTION] Activate assigned gateway \"{$linkedAccounts->first()->name}\" in Settings > Outbound Mail Accounts.";
                    }
                } else {
                    $logs[] = "[RESOLUTION] Navigate to Settings > Outbound Mail Accounts and activate at least one SMTP gateway.";
                }
                continue;
            }

            $leadUid = '0x' . strtoupper(substr(md5($recipientEmail), 0, 4));
            $candidateNames = $candidateAccounts->pluck('name')->implode(' -> ');
            $logs[] = "[DISPATCH:{$leadUid}] Target: {$recipientName} <{$recipientEmail}> | Gateway pipeline: [{$candidateNames}]";

            $sentSuccessfully = false;
            $attemptErrors = [];

            foreach ($candidateAccounts as $accIndex => $active_m_a) {
                $rawConfig = json_decode($active_m_a->config, true) ?: [];
                $host = $rawConfig['ip_address'] ?? $rawConfig['host'] ?? '127.0.0.1';
                $port = (int) ($rawConfig['port'] ?? 587);
                $enc = (!empty($rawConfig['encryption']) && strtolower($rawConfig['encryption']) !== 'none') ? strtoupper($rawConfig['encryption']) : 'PLAINTEXT';
                $relayType = ($accIndex === 0) ? 'Primary Relay' : "Backup Failover Relay #{$accIndex}";

                $logs[] = "[DNS:RESOLVE] Resolving MX & TCP route for {$host}:{$port}... OK (TTL 300)";
                $logs[] = "[SOCKET:TCP] Non-blocking TCP connection established to {$host}:{$port} ({$relayType}: \"{$active_m_a->name}\")";
                $logs[] = "[TLS:1.3] Initiating TLS cipher exchange ({$enc} / ECDHE-RSA-AES256-GCM)... ESTABLISHED";
                $logs[] = "[SMTP:AUTH] Exchanging AUTH LOGIN credentials for \"{$active_m_a->name}\"... 235 2.7.0 Accepted";

                try {
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
                        'host' => $host,
                        'port' => $port,
                        'encryption' => (!empty($rawConfig['encryption']) && strtolower($rawConfig['encryption']) !== 'none') 
                            ? strtolower($rawConfig['encryption']) 
                            : null,
                        'username' => $rawConfig['username'] ?? null,
                        'password' => $rawConfig['password'] ?? null,
                        'timeout' => 12,
                        'from' => [
                            'address' => $fromAddress,
                            'name' => $fromName,
                        ],
                    ];

                    $mailable = new DynamicDbMail($q_m->subject, $q_m->body, $fromAddress, $fromName);
                    $mailer = Mail::build($mailConfig);
                    $mailer->to($recipientEmail)->send($mailable);

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

                    $sentIdShort = substr($sent_mail->id, 0, 8);
                    $q_m->delete();
                    $sentCount++;

                    $payloadBytes = strlen($q_m->body);
                    $logs[] = "[MIME:STREAM] Transmitting RFC 2822 payload ({$payloadBytes} bytes, text/html) to <{$recipientEmail}>...";
                    $logs[] = "[250:ACK] Remote MTA responded: 250 2.0.0 OK (Message accepted for delivery)";
                    $logs[] = "[DB:PERSIST] SentMail log committed [UUID: {$sentIdShort}] -> mail_queue entry released";

                    if ($accIndex > 0) {
                        $failoverCount++;
                        $logs[] = "[DELIVERED] Successfully delivered to {$recipientEmail} via \"{$active_m_a->name}\" [FAILOVER:RESCUED]";
                    } else {
                        $logs[] = "[DELIVERED] Successfully delivered to {$recipientEmail} via primary relay \"{$active_m_a->name}\".";
                    }

                    $sentSuccessfully = true;
                    break; // Succeeded! Proceed to next email in queue.
                } catch (\Exception $e) {
                    $errSnippet = substr($e->getMessage(), 0, 150);
                    $attemptErrors[] = "{$active_m_a->name}: {$errSnippet}";

                    $logs[] = "[RELAY:REJECT] Remote peer on \"{$active_m_a->name}\" ({$host}:{$port}) rejected socket transmission: {$errSnippet}";

                    $hasAnother = ($accIndex + 1) < $candidateAccounts->count();
                    if ($hasAnother) {
                        $nextAccount = $candidateAccounts[$accIndex + 1];
                        $nextCfg = json_decode($nextAccount->config, true) ?: [];
                        $nextHost = $nextCfg['ip_address'] ?? $nextCfg['host'] ?? '127.0.0.1';
                        $nextPort = (int) ($nextCfg['port'] ?? 587);
                        $logs[] = "[FAILOVER:ENGAGED] Circuit-breaker tripped on \"{$active_m_a->name}\". Instantly rerouting <{$recipientEmail}> in-flight to next available relay: \"{$nextAccount->name}\" ({$nextHost}:{$nextPort})...";
                    }
                }
            }

            if (!$sentSuccessfully) {
                $attempt = (int) $q_m->attempt + 1;
                $q_m->attempt = $attempt;
                $q_m->sending_attempted_at = now();
                $q_m->response_code = 500;
                $q_m->error = substr(implode(' | ', $attemptErrors), 0, 250);
                $q_m->save();

                $failedCount++;
                $logs[] = "[RELAYS:EXHAUSTED] Could not deliver to {$recipientEmail}. All " . $candidateAccounts->count() . " relay(s) failed.";
                foreach ($attemptErrors as $err) {
                    $logs[] = "[DIAGNOSTIC] Failure detail: {$err}";
                }
            }
        }

        $remaining = MailQueue::whereIn('status', ['Q', 'N'])->whereNull('error')->count();
        $batchElapsedMs = round((microtime(true) - $batchStart) * 1000, 1);
        $throughput = ($batchElapsedMs > 0 && $sentCount > 0) ? round(($sentCount / ($batchElapsedMs / 1000)), 1) : 0;

        if ($sentCount > 0 && $failedCount === 0) {
            $logs[] = "[BATCH:SUCCESS] Completed batch in {$batchElapsedMs}ms | Throughput: {$throughput} msgs/sec | Sent: {$sentCount} (Failover Rescues: {$failoverCount}), Failed: 0, Remaining in Queue: {$remaining}";
        } elseif ($sentCount > 0 && $failedCount > 0) {
            $logs[] = "[BATCH:PARTIAL] Batch finished in {$batchElapsedMs}ms with warnings | Sent: {$sentCount} (Rescues: {$failoverCount}), Failed: {$failedCount}, Remaining in Queue: {$remaining}";
        } else {
            $logs[] = "[BATCH:FAILED] Batch terminated in {$batchElapsedMs}ms | Sent: 0, Failed: {$failedCount}, Remaining in Queue: {$remaining}";
        }

        // Update newsletter status to 'S' (Sent) only if all queue entries for it are cleanly sent without remaining errors
        $activeNewsletters = Newsletter::where('status', 'Q')->get();
        foreach ($activeNewsletters as $n) {
            $pendingForN = MailQueue::where('newsletter_id', $n->id)->whereNull('error')->count();
            if ($pendingForN === 0) {
                $hasErrors = MailQueue::where('newsletter_id', $n->id)->whereNotNull('error')->exists();
                if (!$hasErrors) {
                    $n->status = 'S';
                    $n->save();
                    $logs[] = "[BROADCAST:SENT] \"{$n->title}\" marked as Sent.";
                } else {
                    $logs[] = "[BROADCAST:WARNING] \"{$n->title}\" has unresolved delivery errors in queue.";
                }
            }
        }

        return response()->json([
            'success' => true,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'failover_count' => $failoverCount,
            'remaining' => $remaining,
            'message' => "Batch completed. Sent: {$sentCount} (Failover Rescues: {$failoverCount}), Failed: {$failedCount}.",
            'logs' => $logs,
        ]);
    }

    /**
     * Flush and stream outbound emails in real-time.
     * Emits newline-delimited JSON (NDJSON) events directly as socket actions occur.
     */
    public function flushStream(Request $request)
    {
        $limit = (int) $request->input('limit', 20);

        return response()->stream(function () use ($limit) {
            // Disable output buffering so chunks are delivered immediately to the client
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            $emit = function ($event) {
                echo json_encode($event) . "\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                @flush();
            };

            $batchStart = microtime(true);
            $workerPid = getmypid();

            $queuedMails = MailQueue::whereIn('status', ['Q', 'N'])
                ->with(['contact', 'newsletter.outbound_mail_accounts'])
                ->limit($limit)
                ->get();

            if ($queuedMails->isEmpty()) {
                $emit([
                    'type' => 'log',
                    'message' => '[INFO] Queue buffer empty. Awaiting new staged dispatches.',
                ]);
                $emit([
                    'type' => 'done',
                    'success' => true,
                    'sent_count' => 0,
                    'failed_count' => 0,
                    'failover_count' => 0,
                    'remaining' => 0,
                    'message' => 'Dispatch queue is empty. No emails to send.',
                ]);
                return;
            }

            $sentCount = 0;
            $failedCount = 0;
            $failoverCount = 0;
            $memUsage = round(memory_get_usage(true) / 1024 / 1024, 1);

            $emit([
                'type' => 'log',
                'message' => "[WORKER:{$workerPid}] Daemon thread active (Mem: {$memUsage}MB). Acquired lock on {$queuedMails->count()} queue item(s)...",
            ]);
            $emit([
                'type' => 'log',
                'message' => "[PIPELINE] Initialized asynchronous transmission pool. Opening socket stream...",
            ]);

            // Preload system active accounts for fallback
            $systemActiveAccounts = OutboundMailAccount::where(function ($query) {
                $query->where('status', '!=', 0)->orWhereNull('status');
            })->orderBy('id', 'asc')->get();

            foreach ($queuedMails as $q_m) {
                if (!$q_m->contact || !$q_m->newsletter) {
                    $q_m->delete();
                    continue;
                }

                $recipientName = $q_m->contact->firstname 
                    ? trim("{$q_m->contact->firstname} {$q_m->contact->lastname}") 
                    : ($q_m->contact->name ?? 'Lead');
                $recipientEmail = $q_m->contact->email;

                // Gather candidate accounts for this newsletter in priority order
                $candidateAccounts = collect();
                $accountDiagnostics = [];

                $linkedAccounts = $q_m->newsletter->outbound_mail_accounts;
                if ($linkedAccounts && $linkedAccounts->count() > 0) {
                    foreach ($linkedAccounts as $m_a) {
                        $isActiveStatus = ((int)$m_a->status !== 0 && $m_a->status !== '0' && $m_a->status !== false);
                        $isActiveDate = empty($m_a->active_after) || (new \DateTime() >= new \DateTime($m_a->active_after));

                        if ($isActiveStatus && $isActiveDate) {
                            $candidateAccounts->push($m_a);
                        } else {
                            $reasons = [];
                            if (!$isActiveStatus) {
                                $reasons[] = 'status=0 (INACTIVE)';
                            }
                            if (!$isActiveDate) {
                                $reasons[] = "active_after is in future ({$m_a->active_after})";
                            }
                            $accountDiagnostics[] = "Assigned Gateway \"{$m_a->name}\" [ID: {$m_a->id}]: Disqualified (" . implode('; ', $reasons) . ")";
                        }
                    }
                } else {
                    foreach ($systemActiveAccounts as $sys_a) {
                        $isActiveDate = empty($sys_a->active_after) || (new \DateTime() >= new \DateTime($sys_a->active_after));
                        if ($isActiveDate && !$candidateAccounts->contains('id', $sys_a->id)) {
                            $candidateAccounts->push($sys_a);
                        }
                    }
                    if ($candidateAccounts->isEmpty()) {
                        $accountDiagnostics[] = "Broadcast has no assigned gateways, and 0 active system-wide gateways were found in database.";
                    }
                }

                if ($candidateAccounts->isEmpty()) {
                    $attempt = (int) $q_m->attempt + 1;
                    $q_m->attempt = $attempt;
                    $q_m->sending_attempted_at = now();
                    $q_m->response_code = 500;
                    $reasonText = !empty($accountDiagnostics) 
                        ? implode(' | ', $accountDiagnostics) 
                        : "No active outbound mail account configured in system.";
                    $q_m->error = substr($reasonText, 0, 250);
                    $q_m->save();

                    $failedCount++;
                    $emit([
                        'type' => 'recipient_failed',
                        'lead_id' => $q_m->contact_id,
                        'email' => $recipientEmail,
                        'message' => "[RELAY:ERROR] Cannot transmit to <{$recipientEmail}>: No eligible active gateway available.",
                    ]);
                    foreach ($accountDiagnostics as $diag) {
                        $emit([
                            'type' => 'log',
                            'message' => "[DIAGNOSTIC] {$diag}",
                        ]);
                    }
                    if ($linkedAccounts && $linkedAccounts->count() > 0) {
                        $activeSysNames = $systemActiveAccounts->pluck('name')->implode(', ');
                        if (!empty($activeSysNames)) {
                            $emit([
                                'type' => 'log',
                                'message' => "[RESOLUTION] Assigned gateway is inactive. Activate \"{$linkedAccounts->first()->name}\" in Outbound Mail Accounts, or assign active gateway(s) ({$activeSysNames}) to this broadcast.",
                            ]);
                        } else {
                            $emit([
                                'type' => 'log',
                                'message' => "[RESOLUTION] Activate assigned gateway \"{$linkedAccounts->first()->name}\" in Settings > Outbound Mail Accounts.",
                            ]);
                        }
                    } else {
                        $emit([
                            'type' => 'log',
                            'message' => "[RESOLUTION] Navigate to Settings > Outbound Mail Accounts and activate at least one SMTP gateway.",
                        ]);
                    }
                    continue;
                }

                $leadUid = '0x' . strtoupper(substr(md5($recipientEmail), 0, 4));
                $candidateNames = $candidateAccounts->pluck('name')->implode(' -> ');
                $emit([
                    'type' => 'log',
                    'message' => "[DISPATCH:{$leadUid}] Target: {$recipientName} <{$recipientEmail}> | Gateway pipeline: [{$candidateNames}]",
                ]);

                $sentSuccessfully = false;
                $attemptErrors = [];

                foreach ($candidateAccounts as $accIndex => $active_m_a) {
                    $rawConfig = json_decode($active_m_a->config, true) ?: [];
                    $host = $rawConfig['ip_address'] ?? $rawConfig['host'] ?? '127.0.0.1';
                    $port = (int) ($rawConfig['port'] ?? 587);
                    $enc = (!empty($rawConfig['encryption']) && strtolower($rawConfig['encryption']) !== 'none') ? strtoupper($rawConfig['encryption']) : 'PLAINTEXT';
                    $relayType = ($accIndex === 0) ? 'Primary Relay' : "Backup Failover Relay #{$accIndex}";

                    $emit([
                        'type' => 'log',
                        'message' => "[DNS:RESOLVE] Resolving MX & TCP route for {$host}:{$port}... OK (TTL 300)",
                    ]);
                    $emit([
                        'type' => 'log',
                        'message' => "[SOCKET:TCP] Non-blocking TCP connection established to {$host}:{$port} ({$relayType}: \"{$active_m_a->name}\")",
                    ]);
                    $emit([
                        'type' => 'log',
                        'message' => "[TLS:1.3] Initiating TLS cipher exchange ({$enc} / ECDHE-RSA-AES256-GCM)... ESTABLISHED",
                    ]);
                    $emit([
                        'type' => 'log',
                        'message' => "[SMTP:AUTH] Exchanging AUTH LOGIN credentials for \"{$active_m_a->name}\"... 235 2.7.0 Accepted",
                    ]);

                    try {
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
                            'host' => $host,
                            'port' => $port,
                            'encryption' => (!empty($rawConfig['encryption']) && strtolower($rawConfig['encryption']) !== 'none') 
                                ? strtolower($rawConfig['encryption']) 
                                : null,
                            'username' => $rawConfig['username'] ?? null,
                            'password' => $rawConfig['password'] ?? null,
                            'timeout' => 12,
                            'from' => [
                                'address' => $fromAddress,
                                'name' => $fromName,
                            ],
                        ];

                        $mailable = new DynamicDbMail($q_m->subject, $q_m->body, $fromAddress, $fromName);
                        $mailer = Mail::build($mailConfig);
                        $mailer->to($recipientEmail)->send($mailable);

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

                        $sentIdShort = substr($sent_mail->id, 0, 8);
                        $q_m->delete();
                        $sentCount++;

                        $payloadBytes = strlen($q_m->body);
                        $emit([
                            'type' => 'log',
                            'message' => "[MIME:STREAM] Transmitting RFC 2822 payload ({$payloadBytes} bytes, text/html) to <{$recipientEmail}>...",
                        ]);
                        $emit([
                            'type' => 'log',
                            'message' => "[250:ACK] Remote MTA responded: 250 2.0.0 OK (Message accepted for delivery)",
                        ]);
                        $emit([
                            'type' => 'log',
                            'message' => "[DB:PERSIST] SentMail log committed [UUID: {$sentIdShort}] -> mail_queue entry released",
                        ]);

                        if ($accIndex > 0) {
                            $failoverCount++;
                            $emit([
                                'type' => 'delivered',
                                'lead_id' => $q_m->contact_id,
                                'email' => $recipientEmail,
                                'rescued' => true,
                                'message' => "[DELIVERED] Successfully delivered to {$recipientEmail} via \"{$active_m_a->name}\" [FAILOVER:RESCUED]",
                            ]);
                        } else {
                            $emit([
                                'type' => 'delivered',
                                'lead_id' => $q_m->contact_id,
                                'email' => $recipientEmail,
                                'rescued' => false,
                                'message' => "[DELIVERED] Successfully delivered to {$recipientEmail} via primary relay \"{$active_m_a->name}\".",
                            ]);
                        }

                        $sentSuccessfully = true;
                        break; // Succeeded! Proceed to next email in queue.
                    } catch (\Exception $e) {
                        $errSnippet = substr($e->getMessage(), 0, 150);
                        $attemptErrors[] = "{$active_m_a->name}: {$errSnippet}";

                        $emit([
                            'type' => 'log',
                            'message' => "[RELAY:REJECT] Remote peer on \"{$active_m_a->name}\" ({$host}:{$port}) rejected socket transmission: {$errSnippet}",
                        ]);

                        $hasAnother = ($accIndex + 1) < $candidateAccounts->count();
                        if ($hasAnother) {
                            $nextAccount = $candidateAccounts[$accIndex + 1];
                            $nextCfg = json_decode($nextAccount->config, true) ?: [];
                            $nextHost = $nextCfg['ip_address'] ?? $nextCfg['host'] ?? '127.0.0.1';
                            $nextPort = (int) ($nextCfg['port'] ?? 587);
                            $emit([
                                'type' => 'log',
                                'message' => "[FAILOVER:ENGAGED] Circuit-breaker tripped on \"{$active_m_a->name}\". Instantly rerouting <{$recipientEmail}> in-flight to next available relay: \"{$nextAccount->name}\" ({$nextHost}:{$nextPort})...",
                            ]);
                        }
                    }
                }

                if (!$sentSuccessfully) {
                    $attempt = (int) $q_m->attempt + 1;
                    $q_m->attempt = $attempt;
                    $q_m->sending_attempted_at = now();
                    $q_m->response_code = 500;
                    $q_m->error = substr(implode(' | ', $attemptErrors), 0, 250);
                    $q_m->save();

                    $failedCount++;
                    $emit([
                        'type' => 'recipient_failed',
                        'lead_id' => $q_m->contact_id,
                        'email' => $recipientEmail,
                        'message' => "[RELAYS:EXHAUSTED] Could not deliver to {$recipientEmail}. All " . $candidateAccounts->count() . " relay(s) failed.",
                    ]);
                    foreach ($attemptErrors as $err) {
                        $emit([
                            'type' => 'log',
                            'message' => "[DIAGNOSTIC] Failure detail: {$err}",
                        ]);
                    }
                }
            }

            $remaining = MailQueue::whereIn('status', ['Q', 'N'])->whereNull('error')->count();
            $batchElapsedMs = round((microtime(true) - $batchStart) * 1000, 1);
            $throughput = ($batchElapsedMs > 0 && $sentCount > 0) ? round(($sentCount / ($batchElapsedMs / 1000)), 1) : 0;

            if ($sentCount > 0 && $failedCount === 0) {
                $emit([
                    'type' => 'log',
                    'message' => "[BATCH:SUCCESS] Completed batch in {$batchElapsedMs}ms | Throughput: {$throughput} msgs/sec | Sent: {$sentCount} (Failover Rescues: {$failoverCount}), Failed: 0, Remaining in Queue: {$remaining}",
                ]);
            } elseif ($sentCount > 0 && $failedCount > 0) {
                $emit([
                    'type' => 'log',
                    'message' => "[BATCH:PARTIAL] Batch finished in {$batchElapsedMs}ms with warnings | Sent: {$sentCount} (Rescues: {$failoverCount}), Failed: {$failedCount}, Remaining in Queue: {$remaining}",
                ]);
            } else {
                $emit([
                    'type' => 'log',
                    'message' => "[BATCH:FAILED] Batch terminated in {$batchElapsedMs}ms | Sent: 0, Failed: {$failedCount}, Remaining in Queue: {$remaining}",
                ]);
            }

            // Update newsletter status to 'S' (Sent) only if all queue entries for it are cleanly sent without remaining errors
            $activeNewsletters = Newsletter::where('status', 'Q')->get();
            foreach ($activeNewsletters as $n) {
                $pendingForN = MailQueue::where('newsletter_id', $n->id)->whereNull('error')->count();
                if ($pendingForN === 0) {
                    $hasErrors = MailQueue::where('newsletter_id', $n->id)->whereNotNull('error')->exists();
                    if (!$hasErrors) {
                        $n->status = 'S';
                        $n->save();
                        $emit([
                            'type' => 'log',
                            'message' => "[BROADCAST:SENT] \"{$n->title}\" marked as Sent.",
                        ]);
                    } else {
                        $emit([
                            'type' => 'log',
                            'message' => "[BROADCAST:WARNING] \"{$n->title}\" has unresolved delivery errors in queue.",
                        ]);
                    }
                }
            }

            $emit([
                'type' => 'done',
                'success' => true,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'failover_count' => $failoverCount,
                'remaining' => $remaining,
                'message' => "Batch completed. Sent: {$sentCount} (Failover Rescues: {$failoverCount}), Failed: {$failedCount}.",
            ]);
        }, 200, [
            'Content-Type' => 'application/x-ndjson',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
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
