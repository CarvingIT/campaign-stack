<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Tag;
use Session;

class ContactController extends Controller
{
    public function list(){
        $contacts = Contact::all();
        return view('contactsmanagement',['contacts'=>$contacts]);
    }

    public function addEditContact($contact_id){
        if($contact_id == 'new'){
            $contact = new Contact();
        }
        else{
            $contact = Contact::find($contact_id);
        }
        $tags = Tag::all();
        return view('contact-form',['contact'=>$contact, 'tags'=>$tags]);
    }

    public function save(Request $request){
        if(empty($request->contact_id)){
            $contact = new Contact();
        }
        else{
            $contact = Contact::find($request->contact_id);
        }
        $contact->firstname = $request->firstname;
        $contact->lastname = $request->lastname;
        $contact->email = $request->email;
        $contact->company = $request->company;
        $contact->mobile = $request->mobile;
        try{
            $contact->save();
            $contact->updateTags($request->tags);
            Session::flash('alert-success', 'Contact saved successfully!');
        }
        catch(\Exception $e){
            Session::flash('alert-danger', "Error has orrcured: Please check. ".$e->getMessage());
        }
        return redirect('/contacts');
    }

    public function deleteContact(Request $request){
        $contact = Contact::find($request->contact_id);
        if(!empty($contact->id)){
            if($contact->delete()){
            Session::flash('alert-success', 'Contact deleted successfully!');
            }
            else{
            Session::flash('alert-danger', "Error has orrcured: Please check again.");
            }
        }
        return redirect('/contacts');
    }


// Class ends
}
