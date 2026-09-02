<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Newsletter;
use App\Models\MailQueue;
use App\Models\SentMail;

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

        // get queued mails 
        MailQueue::whereIn('status', ['Q', 'N']) 
            ->chunk(100, function ($queued) {
                foreach ($queued as $q_m) {
                    if (!$q_m->contact || !$q_m->newsletter) {
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

                    // Fallback to any active outbound mail account if not explicitly linked
                    if (!$active_m_a) {
                        $active_m_a = \App\Models\OutboundMailAccount::where(function ($query) {
                            $query->where('status', '!=', 0)->orWhereNull('status');
                        })->orderBy('id', 'asc')->first();
                    }

                    // Attempt to send mail
                    try {
                        if (!$active_m_a) {
                            throw new \Exception("No active outbound mail account configured in the system.");
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
                        $this->info("Email successfully sent to: {$q_m->contact->email}");
                    } catch (\Exception $e) {
                        // Else add details like attempt, sending_attempted_at, response_code, error
                        $attempt = (int) $q_m->attempt + 1;
                        $q_m->attempt = $attempt;
                        $q_m->sending_attempted_at = now();
                        $q_m->response_code = is_numeric($e->getCode()) && $e->getCode() != 0 ? (int) $e->getCode() : 500;
                        $q_m->error = substr($e->getMessage(), 0, 250);
                        $q_m->save();

                        $this->error("Failed sending to {$q_m->contact->email}: " . $e->getMessage());
                    }
                }
            }); // chunking ends
    }
}
