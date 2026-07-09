<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Newsletter;
use App\Models\Tag;
use App\Models\Contact;
use App\Models\MailQueue;

class QueueMails extends Command
{

    protected $signature = 'CS:queue-mails';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "Creating mail queue.\n";
        $newsletters = Newsletter::where('status','N')->get();
        foreach($newsletters as $n){
            $n->status = 'Q';
            $n->save();
            $tag_ids = json_decode($n->tag_ids);
            foreach($tag_ids as $tid){
                $tag = Tag::find($tid); 
                if(!$tag){ 
                    echo "Tag $tid does not exist; continuing.\n";
                    continue;
                }
                $contacts = $tag->contacts;
                foreach($contacts as $c){
                    $m_q = new MailQueue;                    
                    $m_q->newsletter_id = $n->id;
                    $m_q->contact_id = $c->id;
                    $m_q->status = 'N';
                    $m_q->attempt = 0;
                    $m_q->subject = $this->processTemplate($n->subject_template, $c);
                    $m_q->body = $this->processTemplate($n->body_template, $c);
                    $m_q->save();
                }
            }
        }
    }

    public function processTemplate($template, $contact){
        // substitute vars from $contact and then return
        //
        return $template;
    }
}
