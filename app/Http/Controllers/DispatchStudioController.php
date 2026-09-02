<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\OutboundMailAccount;
use App\Models\MailQueue;
use App\Models\SentMail;
use App\Utils\NewsletterTemplateHandler;
use Illuminate\Support\Str;

class DispatchStudioController extends Controller
{
    /**
     * Show the main Dispatch Studio / Broadcast Workflow Page.
     */
    public function index(Request $request)
    {
        $newsletters = Newsletter::with(['campaign', 'newsletter_tags.tag', 'outbound_mail_accounts'])
            ->orderBy('created_at', 'desc')
            ->get();

        $outboundAccounts = OutboundMailAccount::where('status', '!=', 0)
            ->orWhereNull('status')
            ->get();

        $tags = Tag::withCount('contacts')->get();

        $selectedNewsletterId = $request->query('newsletter_id', $newsletters->first()->id ?? null);
        $selectedNewsletter = $selectedNewsletterId ? Newsletter::with(['campaign', 'newsletter_tags.tag', 'outbound_mail_accounts'])->find($selectedNewsletterId) : null;

        $inQueue = MailQueue::where('status', 'Q')->count();
        $sentToday = SentMail::whereDate('created_at', now()->toDateString())->count();
        $failedCount = MailQueue::whereNotNull('error')->count();

        return view('dispatch_studio', [
            'newsletters' => $newsletters,
            'outboundAccounts' => $outboundAccounts,
            'tags' => $tags,
            'selectedNewsletter' => $selectedNewsletter,
            'inQueue' => $inQueue,
            'sentToday' => $sentToday,
            'failedCount' => $failedCount,
        ]);
    }

    /**
     * Fetch resolved audience contacts for a specific newsletter or tag list.
     */
    public function getAudience(Request $request)
    {
        $newsletterId = $request->input('newsletter_id');
        $newsletter = Newsletter::with('newsletter_tags')->find($newsletterId);

        if (!$newsletter) {
            return response()->json(['success' => false, 'message' => 'Broadcast not found.'], 404);
        }

        $tagIds = $newsletter->newsletter_tags->pluck('tag_id')->toArray();

        if (empty($tagIds)) {
            $contacts = Contact::with('tags')->get();
        } else {
            $contacts = Contact::whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })->with('tags')->get();
        }

        $templateHandler = new NewsletterTemplateHandler();

        $data = $contacts->map(function ($c) use ($templateHandler, $newsletter) {
            return [
                'id' => $c->id,
                'email' => $c->email,
                'name' => trim(($c->salutation ?? '') . ' ' . ($c->firstname ?? '') . ' ' . ($c->lastname ?? '')),
                'firstname' => $c->firstname ?? '',
                'lastname' => $c->lastname ?? '',
                'company' => $c->company ?? '—',
                'mobile' => $c->mobile ?? '—',
                'tags' => $c->tags->pluck('label')->toArray(),
                'preview_subject' => $templateHandler->process($newsletter->subject_template, $c),
                'preview_body' => $templateHandler->process($newsletter->body_template, $c),
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'contacts' => $data,
            'newsletter' => [
                'id' => $newsletter->id,
                'title' => $newsletter->title,
                'subject_template' => $newsletter->subject_template,
                'body_template' => $newsletter->body_template,
                'status' => $newsletter->status,
            ]
        ]);
    }

    /**
     * Queue only user-approved/selected contacts for this specific broadcast.
     */
    public function queueCustom(Request $request)
    {
        $newsletterId = $request->input('newsletter_id');
        $contactIds = $request->input('contact_ids', []);

        if (empty($newsletterId)) {
            return response()->json(['success' => false, 'message' => 'Please select a broadcast.'], 422);
        }

        if (empty($contactIds) || !is_array($contactIds)) {
            return response()->json(['success' => false, 'message' => 'No contacts were selected to queue.'], 422);
        }

        $newsletter = Newsletter::findOrFail($newsletterId);
        $newsletter->status = 'Q';
        $newsletter->save();

        $templateHandler = new NewsletterTemplateHandler();
        $totalQueued = 0;
        $logs = [];
        $logs[] = "[QUEUE INITIALIZED] Broadcast: \"{$newsletter->title}\" (Targeting " . count($contactIds) . " selected leads)";

        foreach ($contactIds as $cid) {
            $contact = Contact::find($cid);
            if (!$contact || empty($contact->email)) {
                continue;
            }

            // Check if already in queue for this newsletter
            $existing = MailQueue::where('newsletter_id', $newsletter->id)
                ->where('contact_id', $contact->id)
                ->first();

            if ($existing) {
                continue;
            }

            $mailQueue = new MailQueue();
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

        $logs[] = "[COMPLETE] Generated {$totalQueued} personalized queue record(s) ready for transmission.";

        return response()->json([
            'success' => true,
            'count' => $totalQueued,
            'message' => "Successfully queued {$totalQueued} personalized email(s).",
            'logs' => $logs,
        ]);
    }

    /**
     * Update a contact quickly inline before queueing.
     */
    public function updateContactQuick(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'email' => 'required|email|max:255',
            'firstname' => 'nullable|string|max:100',
            'lastname' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $contact = Contact::findOrFail($request->input('contact_id'));

            // Check if email changed and if another contact already has this email
            $newEmail = trim($request->input('email'));
            if (strtolower($newEmail) !== strtolower($contact->email)) {
                $duplicate = Contact::where('email', $newEmail)->where('id', '!=', $contact->id)->first();
                if ($duplicate) {
                    return response()->json([
                        'success' => false,
                        'message' => "The email '{$newEmail}' is already assigned to lead #{$duplicate->id} ({$duplicate->firstname} {$duplicate->lastname}). Please use a unique email address.",
                    ], 422);
                }
            }

            $contact->firstname = $request->input('firstname', $contact->firstname);
            $contact->lastname = $request->input('lastname', $contact->lastname);
            $contact->email = $newEmail;
            $contact->company = $request->input('company', $contact->company);
            $contact->save();

            return response()->json([
                'success' => true,
                'message' => "Updated contact details for {$contact->email}",
                'contact' => $contact,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact: ' . $e->getMessage(),
            ], 500);
        }
    }
}
