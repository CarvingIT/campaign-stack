<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use Session;

class CampaignController extends Controller
{
    public function list(){
        $campaigns = Campaign::withCount('newsletters')->get();
        return view('campaignsmanagement',['campaigns'=>$campaigns]);
    }

    public function addEditCampaign($campaign_id){
        if($campaign_id == 'new'){
            $campaign = new Campaign();
        }
        else{
            $campaign = Campaign::find($campaign_id);
        }
        return view('campaign-form',['campaign'=>$campaign]);
    }

    public function save(Request $request){
        if(empty($request->campaign_id)){
            $campaign = new Campaign();
        }
        else{
            $campaign = Campaign::find($request->campaign_id);
        }
        $campaign->name = $request->name;
        $attributes_array = array('type'=>$request->campaign_type);
        $other_attributes = json_encode($attributes_array);
        $campaign->other_attributes = $other_attributes;
        try{
            $campaign->save();
            Session::flash('alert-success', 'Campaign saved successfully!');
        }
        catch(\Exception $e){
            Session::flash('alert-danger', "Error has orrcured: Please check. ".$e->getMessage());
        }
        return redirect('/campaigns');
    }

    public function deleteCampaign(Request $request){
        $campaign = Campaign::find($request->campaign_id);
        if(!empty($campaign->id)){
            if($campaign->delete()){
            Session::flash('alert-success', 'Campaign deleted successfully!');
            }
            else{
            Session::flash('alert-danger', "Error has orrcured: Please check again.");
            }
        }
        return redirect('/campaigns');
    }


// Class ends
}
