<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Session;

class TagController extends Controller
{
    public function list(){
        $tags = Tag::all();
        return view('tagsmanagement',['tags'=>$tags]);
    }

    public function addEditTag($tag_id){
        if($tag_id == 'new'){
            $tag = new Tag();
        }
        else{
            $tag = Tag::find($tag_id);
        }
        return view('tag-form',['tag'=>$tag]);
    }

    public function save(Request $request){
        if(empty($request->tag_id)){
            $tag = new Tag();
        }
        else{
            $tag = Tag::find($request->tag_id);
        }
        $tag->label = $request->label;
        try{
            $tag->save();
            Session::flash('alert-success', 'Tag saved successfully!');
        }
        catch(\Exception $e){
            Session::flash('alert-danger', "Error has orrcured: Please check. ".$e->getMessage());
        }
        return redirect('/tags');
    }

    public function deleteTag(Request $request){
        $tag = Tag::find($request->tag_id);
        if(!empty($tag->id)){
            if($tag->delete()){
            Session::flash('alert-success', 'Tag deleted successfully!');
            }
            else{
            Session::flash('alert-danger', "Error has orrcured: Please check again.");
            }
        }
        return redirect('/tags');
    }


// Class ends
}
