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
        $account->type = $request->account_type;
        $config = array('username'=>$request->account_username,'password'=>$request->account_password,
                        'ip_address'=>$request->account_ip_address,'port'=>$request->account_port);
        $json_config = json_encode($config);
        //echo $json_config; exit;
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


// Class ends here
}
