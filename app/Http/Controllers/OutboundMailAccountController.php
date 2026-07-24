<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutboundMailAccount;
use Session;

class OutboundMailAccountController extends Controller
{
    public function list(Request $req){
        $accounts = OutboundMailAccount::all();
        return view('mailaccounts_management',['accounts'=>$accounts]);
    }

    public function addEditAccount($account_id){
        if($account_id == 'new'){
            $account = new OutboundMailAccount(); 
        }
        else{
            $account = OutboundMailAccount::find($account_id);
        }
        return view('outbound_mail_accounts',['account'=>$account]);
    }

    public function save(Request $request){
        if(empty($request->account_id)){
            $account = new OutboundMailAccount();
        }
        else{
            $account = OutboundMailAccount::find($request->account_id);
        }

        $account->name = $request->account_name;
        $account->status = $request->account_status;
        $account->type = $request->account_type;
	if($request->account_type == 'SMTP'){
        	$config = array('username'=>$request->account_username,'password'=>$request->account_password,
                        'ip_address'=>$request->account_ip_address,'port'=>$request->account_port,
                        'encryption'=>$request->account_encryption, 
                        'from_username'=>$request->account_from_username,
                        'from_address'=>$request->account_from_address);
	}
	else if($request->account_type == 'API'){
		$label = $request->label;
		$value = $request->value;
		for($i = 0; $i<count($request->label); $i++){
		$config[] = array('key'=>$label[$i],'value' => $value[$i]);
		}
		//print_r(json_encode($config));
	}
        $json_config = json_encode($config);
        $account->config = $json_config;

         try{
            $account->save();
            Session::flash('alert-success', 'Account saved successfully!');
         }
         catch(\Exception $e){
            Session::flash('alert-danger', "Error has orrcured: Please check. ".$e->getMessage());
         }
        return redirect('/mail-accounts');

    }

    public function viewAccount($account_id){
        $account = OutboundMailAccount::find($account_id);
        return view('accountdetails',['account'=>$account]);
    }
    
    public function deleteAccount(Request $request){
        $account = OutboundMailAccount::find($request->account_id);
        if(!empty($account->id)){
            if($account->delete()){
                Session::flash('alert-success', 'Account deleted successfully!');
            }
            else{
                Session::flash('alert-danger', "Error has orrcured: Please check. ".$e->getMessage());
            }
        }
        return redirect('/mail-accounts');
    }

// Class ends here
}
