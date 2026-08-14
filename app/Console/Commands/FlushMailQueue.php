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
        echo "Flushing mail queue.\n";
        // get queued mails 
        $queued_mails = MailQueue::where('status','Q') 
            ->chunk(100, function($queued){
            foreach($queued as $q_m){
                $mail_accounts = $q_m->newsletter->outbound_mail_accounts; 
                foreach($mail_accounts as $m_a){
                    $active_after_date_time = new \DateTime($m_a->active_after);
                    $current_date_time = new \DateTime();
                    $active_m_a = null; 
                    if($current_date_time > $active_after_date_time){
                        // use this mail account
                        $active_m_a = $m_a;
                        break;
                    }
                }
                    // attempt to send mail
                    try{
                        $mailable = new DynamicDbMail($q_m->subject, $q_m->body);
                        $mailer = Mail::build(json_decode($active_m_a->config));
                        $mailer->to($q_m->contact->email)->send($mailable);

                        // on success add an entry in the sent_mails
                        $sent_mail = new SentMail;
                        $sent_mail->newsletter_id = $q_m->newsletter_id;
                        $sent_mail->outbound_mail_account_id = $active_m_a->id;
                        $sent_mail->contact_id = $q_m->contact_id;
                        $sent_mail->subject = $q_m->subject;
                        $sent_mail->body = $q_m->body;
                        $sent_mail->save();
                        // remove $q_m
                        $q_m->delete();
                    }
                    catch(\Exception $e){
                        // else add details like attempt, sending_attempted_at, response_code, error
                        // for the current $q_m record and save
                        $attempt = (int) $q_m->attempt;
                        $attempt++;
                        $q_m->attempt = $attempt;
                        $q_m->sending_attempted_at = now();
                        $q_m->response_code = $e->getCode();
                        $q_m->error = $e->getMessage();
                        $q_m->save();
                    }
                }
        }); // chunking ends
    }
}
