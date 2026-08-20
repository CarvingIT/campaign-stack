<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Newsletter;
use App\Models\Contact;
use App\Models\MailQueue;
use App\Utils\NewsletterTemplateHandler;

class QueueMails extends Command
{
    protected $signature = 'CS:queue-mails';

    public function handle()
    {
        echo "Creating mail queue.\n";

        $templateHandler = new NewsletterTemplateHandler();

        $newsletters = Newsletter::where('status', 'N')->get();

        foreach ($newsletters as $n) {

            $n->status = 'Q';
            $n->save();

            $contactIds = collect();

            foreach ($n->newsletter_tags as $newsletterTag) {

                $tag = $newsletterTag->tag;

                if (!$tag) {
                    echo "Tag does not exist; continuing.\n";
                    continue;
                }

                $contactIds = $contactIds->merge(
                    $tag->contacts->pluck('id')
                );
            }

            // One email per unique contact
            $contactIds = $contactIds->unique();

            foreach ($contactIds as $contactId) {

                $contact = Contact::find($contactId);

                if (!$contact) {
                    continue;
                }

                $mailQueue = new MailQueue;

                // MailQueue uses UUID as primary key
                $mailQueue->id = (string) Str::uuid();

                $mailQueue->newsletter_id = $n->id;
                $mailQueue->contact_id = $contact->id;
                $mailQueue->status = 'N';
                $mailQueue->attempt = 0;

                $mailQueue->subject = $templateHandler->process(
                    $n->subject_template,
                    $contact
                );

                $mailQueue->body = $templateHandler->process(
                    $n->body_template,
                    $contact
                );

                $mailQueue->save();
            }
        }

        echo "Mail queue created successfully.\n";
    }
}