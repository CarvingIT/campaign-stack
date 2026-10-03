<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\OutboundMailAccount;
use App\Models\NewsletterOutboundMailAccount;
use Session;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function list(){
        $newsletters = Newsletter::withCount(['sent_mails','queued_mails'])
            ->with(['campaign', 'newsletter_tags.tag'])
            ->latest()
            ->get();
        return view('newslettersmanagement',['newsletters'=>$newsletters]);
    }

    public function addEditNewsletter($newsletter_id){
        if($newsletter_id == 'new'){
            $newsletter = new Newsletter();
        }
        else{
            $newsletter = Newsletter::with(['newsletter_tags', 'newsletter_outbound_mail_accounts'])->find($newsletter_id);
        }
        $campaigns = Campaign::all();














        $tags = Tag::withCount('contacts')->orderBy('label')->get();
        $mailAccounts = OutboundMailAccount::all();
        $outboundAccounts = $mailAccounts;
        $selectedMailAccountId = $newsletter->newsletter_outbound_mail_accounts->first()?->outbound_mail_account_id;
        $sampleContact = \App\Models\Contact::first();


















        return view('newsletter-form', [
            'newsletter' => $newsletter,
            'campaigns' => $campaigns,
            'tags' => $tags,


























            'mailAccounts' => $mailAccounts,
            'selectedMailAccountId' => $selectedMailAccountId,
            'outboundAccounts' => $outboundAccounts,
            'sampleContact' => $sampleContact,






























        ]);
    }

    public function save(Request $request){
        if(empty($request->newsletter_id)){
            $newsletter = new Newsletter();
            $newsletter->uuid = (string) Str::uuid();
        }
        else{
            $newsletter = Newsletter::find($request->newsletter_id);
        }
        $newsletter->title = $request->title;
        $newsletter->campaign_id = $request->campaign_id;
        $newsletter->subject_template = $request->subject_template;
        $newsletter->body_template = $request->body_template;
        $newsletter->status = $request->status;
        try{
            $newsletter->save();





























            if ($request->exists('outbound_account_ids')) {
                $newsletter->updateOutboundAccounts(
                    $request->input('outbound_account_ids', [])
                );
            } elseif ($request->filled('outbound_mail_account_id')) {
                $newsletter->updateOutboundAccounts([
                    $request->input('outbound_mail_account_id')
                ]);
            }

            $newsletter->updateTags($request->input('tag_ids', []));
            Session::flash('alert-success', 'Newsletter saved successfully!');































        }
        catch(\Exception $e){
            Session::flash('alert-danger', "Error has occurred: Please check. ".$e->getMessage());
        }
        return redirect('/newsletters');
    }

    public function deleteNewsletter(Request $request){
        $newsletter = Newsletter::find($request->newsletter_id);
        if(!empty($newsletter->id)){
            if($newsletter->delete()){
            Session::flash('alert-success', 'Newsletter deleted successfully!');
            }
            else{
            Session::flash('alert-danger', "Error has orrcured: Please check again.");
            }
        }
        return redirect('/newsletters');
    }


// Class ends
}
