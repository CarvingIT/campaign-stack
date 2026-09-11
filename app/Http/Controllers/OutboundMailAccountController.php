<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutboundMailAccount;
use Session;

class OutboundMailAccountController extends Controller
{
    public function list(Request $req){
        $accounts = OutboundMailAccount::orderBy('status', 'desc')->latest()->get();
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
        $account->status = $request->account_status ?? 1;
        $account->type = $request->account_type;
        if (empty($account->active_after)) {
            $account->active_after = now()->subMinute();
        }
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
                Session::flash('alert-danger', "Error has orrcured: Please check.");
            }
        }
        return redirect('/mail-accounts');
    }

    public function testConnection(Request $request)
    {
        $account = OutboundMailAccount::findOrFail($request->account_id);
        $recipient = $request->input('recipient', auth()->user()->email ?? 'test@example.com');
        $rawConfig = json_decode($account->config, true) ?: [];

        if ($account->type !== 'SMTP') {
            return response()->json([
                'success' => false,
                'message' => 'Test connection is currently supported for SMTP accounts.',
            ], 422);
        }

        try {
            $fromAddress = !empty($rawConfig['from_address']) 
                ? $rawConfig['from_address'] 
                : (!empty($rawConfig['username']) && filter_var($rawConfig['username'], FILTER_VALIDATE_EMAIL) 
                    ? $rawConfig['username'] 
                    : config('mail.from.address', 'noreply@campaignstack.in'));

            $fromName = !empty($rawConfig['from_username']) ? $rawConfig['from_username'] : 'Campaign Stack';

            $mailConfig = [
                'transport' => 'smtp',
                'host' => $rawConfig['ip_address'] ?? $rawConfig['host'] ?? '127.0.0.1',
                'port' => (int) ($rawConfig['port'] ?? 587),
                'encryption' => (!empty($rawConfig['encryption']) && strtolower($rawConfig['encryption']) !== 'none') 
                    ? strtolower($rawConfig['encryption']) 
                    : null,
                'username' => $rawConfig['username'] ?? null,
                'password' => $rawConfig['password'] ?? null,
                'timeout' => 15,
                'from' => [
                    'address' => $fromAddress,
                    'name' => $fromName,
                ],
            ];

            $subject = "Campaign Stack: SMTP Test Connection Successful 🚀";
            $html = "<h3>SMTP Connection Verified!</h3><p>Your mail server <strong>" . e($account->name) . "</strong> is properly configured and successfully sending emails.</p><p><em>Dispatched at: " . now()->toDateTimeString() . "</em></p>";

            $mailable = new \App\Mail\DynamicDbMail($subject, $html, $fromAddress, $fromName);
            $mailer = \Illuminate\Support\Facades\Mail::build($mailConfig);
            $mailer->to($recipient)->send($mailable);

            return response()->json([
                'success' => true,
                'message' => "Test email successfully sent to {$recipient}!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

// Class ends here
}
