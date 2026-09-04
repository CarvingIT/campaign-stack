<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Newsletter;
use App\Models\MailQueue;
use App\Models\SentMail;
use App\Models\OutboundMailAccount;

use App\Mail\DynamicDbMail;
use Illuminate\Support\Facades\Mail;

class FlushMailQueue extends Command
{
    protected $signature = 'CS:FlushMailQueue';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = MailQueue::whereIn('status', ['Q', 'N'])->count();
        $this->info("Flushing mail queue. Found {$count} email(s) in queue.");

        if ($count === 0) {
            $this->warn("No queued emails to send. Make sure you run 'php artisan CS:queue-mails' with ready newsletters.");
            return;
        }

        // Preload active accounts across the system for fallback
        $systemActiveAccounts = OutboundMailAccount::where(function ($query) {
            $query->where('status', '!=', 0)->orWhereNull('status');
        })->orderBy('id', 'asc')->get();

        // get queued mails 
        MailQueue::whereIn('status', ['Q', 'N']) 
            ->chunk(100, function ($queued) use ($systemActiveAccounts) {
                foreach ($queued as $q_m) {
                    if (!$q_m->contact || !$q_m->newsletter) {
                        continue;
                    }

                    // Collect candidate outbound accounts in priority order
                    $candidateAccounts = collect();

                    if ($q_m->newsletter->outbound_mail_accounts && $q_m->newsletter->outbound_mail_accounts->count() > 0) {
                        foreach ($q_m->newsletter->outbound_mail_accounts as $m_a) {
                            $isActiveDate = empty($m_a->active_after) || (new \DateTime() >= new \DateTime($m_a->active_after));
                            if ($isActiveDate && (int)$m_a->status !== 0) {
                                $candidateAccounts->push($m_a);
                            }
                        }
                    } else {
                        // Fallback to active outbound mail accounts if newsletter has none
                        foreach ($systemActiveAccounts as $s_a) {
                            $isActiveDate = empty($s_a->active_after) || (new \DateTime() >= new \DateTime($s_a->active_after));
                            if ($isActiveDate && !$candidateAccounts->contains('id', $s_a->id)) {
                                $candidateAccounts->push($s_a);
                            }
                        }
                    }

                    if ($candidateAccounts->isEmpty()) {
                        $this->error("No active outbound mail account configured in the system for {$q_m->contact->email}");
                        continue;
                    }

                    $sentSuccessfully = false;
                    $attemptErrors = [];

                    foreach ($candidateAccounts as $accIndex => $active_m_a) {
                        try {
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
                                'timeout' => 12,
                                'from' => [
                                    'address' => $fromAddress,
                                    'name' => $fromName,
                                ],
                            ];

                            $mailable = new DynamicDbMail($q_m->subject, $q_m->body, $fromAddress, $fromName);
                            $mailer = Mail::build($mailConfig);
                            $mailer->to($q_m->contact->email)->send($mailable);

                            // On success add an entry in the sent_mails
                            $sent_mail = new SentMail;
                            $sent_mail->id = (string) \Illuminate\Support\Str::uuid();
                            $sent_mail->newsletter_id = $q_m->newsletter_id;
                            $sent_mail->outbound_mail_account_id = $active_m_a->id;
                            $sent_mail->contact_id = $q_m->contact_id;
                            $sent_mail->subject = $q_m->subject;
                            $sent_mail->body = $q_m->body;
                            $sent_mail->save();

                            // Remove from queue
                            $q_m->delete();
                            if ($accIndex > 0) {
                                $this->info("Email successfully sent to: {$q_m->contact->email} via {$active_m_a->name} (Failover recovery)");
                            } else {
                                $this->info("Email successfully sent to: {$q_m->contact->email} via {$active_m_a->name}");
                            }

                            $sentSuccessfully = true;
                            break; // Stop checking subsequent accounts for this mail
                        } catch (\Exception $e) {
                            $errSnippet = substr($e->getMessage(), 0, 80);
                            $attemptErrors[] = "{$active_m_a->name}: {$errSnippet}";

                            $hasAnother = ($accIndex + 1) < $candidateAccounts->count();
                            if ($hasAnother) {
                                $nextAccount = $candidateAccounts[$accIndex + 1];
                                $this->warn("Account {$active_m_a->name} failed for {$q_m->contact->email}. Failing over to next account: {$nextAccount->name}...");
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

                        $this->error("Failed sending to {$q_m->contact->email} after trying " . $candidateAccounts->count() . " account(s).");
                    }
                }
            }); // chunking ends
    }
}
