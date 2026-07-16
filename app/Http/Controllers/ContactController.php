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
        $tags = Tag::orderBy('label')->get();
        return view('contact-form',['contact'=>$contact, 'tags'=>$tags]);
    }

    public function importForm(){
        $tags = Tag::orderBy('label')->get();
        return view('import-contact-form', ['tags'=>$tags]);
    }

    public function import(Request $request){
        $request->validate([
            'contacts' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('contacts');
        $handle = fopen($file->getRealPath(), 'r');

        $headers = fgetcsv($handle, 1000, ','); 
        $batch = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Combine headers with row values
            $rowData = array_combine($headers, $row);

            // Structure to match database columns
            $batch[] = [
                'salutation'       => $rowData['salutation'],
                'firstname'       => $rowData['firstname'],
                'lastname'       => $rowData['lastname'],
                'email'      => $rowData['email'],
                'company'      => $rowData['company'],
                'mobile'      => $rowData['mobile'],
            ];
        }
        fclose($handle);

        foreach($batch as $b){
            try{
            $c = Contact::create($b);
            $c->updateTags($request->tags);
            Session::flash('alert-success', 'Contacts imported successfully!');
            }
            catch(\Exception $e){
                Session::flash('alert-danger', "Some contacts were not imported perhaps because they are already present in the database.");
            }
        }
        return redirect('/contacts');
    }

    public function save(Request $request){
        if(empty($request->contact_id)){
            $contact = new Contact();
        }
        else{
            $contact = Contact::find($request->contact_id);
        }
        $contact->salutation = $request->salutation;
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
