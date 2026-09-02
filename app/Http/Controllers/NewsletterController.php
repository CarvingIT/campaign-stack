<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\Campaign;
use App\Models\Tag;
use Session;
use Illuminate\Support\Str; 

class NewsletterController extends Controller
{
    public function list(){
        $newsletters = Newsletter::withCount(['sent_mails','queued_mails'])->get();
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
        $tags = Tag::withCount('contacts')->get();
        $outboundAccounts = \App\Models\OutboundMailAccount::all();
        $sampleContact = \App\Models\Contact::first();

        return view('newsletter-form', [
            'newsletter' => $newsletter,
            'campaigns' => $campaigns,
            'tags' => $tags,
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
            $newsletter->updateTags($request->tag_ids ?: []);
            $newsletter->updateOutboundAccounts($request->outbound_account_ids ?: []);
            Session::flash('alert-success', 'Broadcast saved successfully!');
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
